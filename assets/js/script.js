/**
 * Sistema de Currículos
 * Script para gerenciar o formulário, adicionar entradas dinâmicas e gerar PDF
 */
class CurriculoApp {
    constructor() {
        this.form = document.getElementById('curriculoForm');
        this.alert = document.getElementById('alert');
        this.fotoPreview = document.getElementById('fotoPreview');
        this.initialize();
    }

    initialize() {
        if (!this.form) return;
        
        // Inicializar máscaras de entrada
        this.setupInputMasks();
        
        // Configurar eventos
        this.setupEventListeners();
        
        // Configurar seções dinâmicas
        this.setupDynamicSections();
        
        // Configurar geração de PDF
        this.setupPDFGeneration();
    }

    setupInputMasks() {
        // Máscara para CPF
        const cpfInput = document.getElementById('cpf');
        if (cpfInput) {
            cpfInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                
                // Formatar CPF (000.000.000-00)
                if (value.length <= 3) {
                    // Nada a fazer
                } else if (value.length <= 6) {
                    value = value.replace(/(\d{3})(\d+)/, '$1.$2');
                } else if (value.length <= 9) {
                    value = value.replace(/(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
                } else {
                    value = value.replace(/(\d{3})(\d{3})(\d{3})(\d+)/, '$1.$2.$3-$4');
                }
                
                e.target.value = value;
            });
        }

        // Máscara para telefone
        const telefoneInput = document.getElementById('telefone');
        if (telefoneInput) {
            telefoneInput.addEventListener('input', (e) => {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 11) value = value.slice(0, 11);
                
                // Formatar telefone ((00) 00000-0000)
                if (value.length <= 2) {
                    // Nada a fazer
                } else if (value.length <= 6) {
                    value = value.replace(/(\d{2})(\d+)/, '($1) $2');
                } else if (value.length <= 10) {
                    value = value.replace(/(\d{2})(\d{4})(\d+)/, '($1) $2-$3');
                } else {
                    value = value.replace(/(\d{2})(\d{5})(\d+)/, '($1) $2-$3');
                }
                
                e.target.value = value;
            });
        }
    }

    setupEventListeners() {
        // Preview da foto
        const fotoInput = document.getElementById('foto');
        if (fotoInput) {
            fotoInput.addEventListener('change', (e) => {
                this.handlePhotoUpload(e.target.files[0]);
            });
        }

        // Envio do formulário
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleFormSubmit();
        });
    }

    setupDynamicSections() {
        // Adicionar formação
        const addFormacao = document.getElementById('addFormacao');
        if (addFormacao) {
            addFormacao.addEventListener('click', () => {
                this.cloneSection('formacoesContainer');
            });
        }

        // Adicionar experiência
        const addExperiencia = document.getElementById('addExperiencia');
        if (addExperiencia) {
            addExperiencia.addEventListener('click', () => {
                this.cloneSection('experienciasContainer');
            });
        }

        // Configurar remoção de entradas
        document.querySelectorAll('.entries-container').forEach(container => {
            container.addEventListener('click', (e) => {
                if (e.target.closest('.btn-remove')) {
                    const entry = e.target.closest('.form-entry');
                    if (entry && !entry.classList.contains('entry-template')) {
                        entry.remove();
                        // Opcional: Reindexar os campos se necessário
                    }
                }
            });
        })};

    setupPDFGeneration() {
        const pdfBtn = document.getElementById('gerarPDF');
        const modal = document.getElementById('pdfModal');
        const closeBtn = document.querySelector('.close');
        const confirmBtn = document.getElementById('confirmarCPF');

        if (pdfBtn) {
            pdfBtn.addEventListener('click', async () => {
                try {
                    const cpfs = await this.fetchCPFs();
                    if (cpfs && cpfs.length > 0) {
                        this.populateCPFModal(cpfs);
                        modal.style.display = 'block';
                    } else {
                        this.showAlert('Nenhum currículo encontrado.', 'info');
                    }
                } catch (error) {
                    this.showAlert('Erro ao buscar CPFs cadastrados.', 'error');
                }
            });
        }

        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                modal.style.display = 'none';
            });
        }

        if (confirmBtn) {
            confirmBtn.addEventListener('click', () => {
                const select = document.getElementById('cpfList');
                if (select && select.value) {
                    window.open(`php/gerar-pdf.php?cpf=${select.value}`, '_blank');
                    modal.style.display = 'none';
                } else {
                    this.showAlert('Selecione um CPF primeiro.', 'warning');
                }
            });
        }

        // Fechar modal ao clicar fora
        window.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });
    }

    async handleFormSubmit() {
        try {
            // Desabilitar botão de envio para evitar múltiplos cliques
            const submitBtn = this.form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Salvando...';
            }
            
            // Coletar e validar dados
            const formData = this.collectFormData();
            if (!this.validateForm(formData)) {
                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-save"></i> Salvar Currículo';
                }
                return;
            }
            
            // Preparar payload com foto processada
            const payload = await this.createFormPayload(formData);
            
            // Enviar dados
            const response = await fetch('php/salvar.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });

            const result = await response.json();
            
            if (!response.ok) {
                throw new Error(result.error || 'Erro ao salvar currículo');
            }

            this.showAlert('Currículo salvo com sucesso!', 'success');
            this.resetForm();
            
        } catch (error) {
            this.showAlert(error.message, 'error');
            console.error('Erro:', error);
        } finally {
            // Reativar botão de envio
            const submitBtn = this.form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Salvar Currículo';
            }
        }
    }

    collectFormData() {
        return {
            nome: document.getElementById('nome')?.value.trim() || '',
            cpf: document.getElementById('cpf')?.value.replace(/\D/g, '') || '',
            telefone: document.getElementById('telefone')?.value.replace(/\D/g, '') || '',
            cidade: document.getElementById('cidade')?.value.trim() || '',
            habilitado: document.getElementById('habilitado')?.value || 'nao',
            foto: document.getElementById('foto')?.files[0] || null,
            formacoes: this.collectSectionData('formacoesContainer'),
            experiencias: this.collectSectionData('experienciasContainer')
        };
    }

    async createFormPayload(data) {
        const payload = {
            nome: data.nome,
            cpf: data.cpf,
            telefone: data.telefone,
            cidade: data.cidade,
            habilitado: data.habilitado,
            formacoes: data.formacoes,
            experiencias: data.experiencias
        };

        // Processar foto se existir
        if (data.foto) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.onload = () => {
                    payload.foto = {
                        data: reader.result.split(',')[1], // Remove o prefixo do base64
                        type: data.foto.type
                    };
                    resolve(payload);
                };
                reader.readAsDataURL(data.foto);
            });
        }

        return payload;
    }

    validateForm(data) {
        const errors = [];
        
        // Validação básica
        if (!data.nome) errors.push('Nome é obrigatório');
        if (data.cpf.length !== 11) errors.push('CPF inválido');
        if (!data.telefone || data.telefone.length < 10) errors.push('Telefone inválido');
        if (!data.cidade) errors.push('Cidade é obrigatória');
        
        // Validação das seções dinâmicas
        const formacoes = data.formacoes.filter(formacao => 
            formacao.length > 0 && formacao[0].trim() !== '');
            
        const experiencias = data.experiencias.filter(exp => 
            exp.length > 0 && exp[0].trim() !== '' && exp[1].trim() !== '');
            
        if (formacoes.length === 0) errors.push('Adicione pelo menos uma formação acadêmica');
        if (experiencias.length === 0) errors.push('Adicione pelo menos uma experiência profissional');

        if (errors.length > 0) {
            this.showAlert(errors.join('<br>'), 'error');
            return false;
        }
        
        return true;
    }

    cloneSection(containerId) {
        const container = document.getElementById(containerId);
        const template = container.querySelector('.entry-template');
        
        if (template) {
            // Clonar o template
            const clone = template.cloneNode(true);
            clone.classList.remove('entry-template');
            
            // Habilitar os campos no clone
            clone.querySelectorAll('input, textarea, select').forEach(input => {
                input.disabled = false;
                // Remover valores padrão do template (opcional)
                if (!input.hasAttribute('data-keep-value')) {
                    input.value = '';
                }
            });
            
            // Inserir o clone antes do botão de adicionar (se aplicável)
            container.appendChild(clone);
        }
    }

    collectSectionData(containerId) {
        const container = document.getElementById(containerId);
        if (!container) return [];
        
        const entries = Array.from(container.children).filter(
            entry => !entry.classList.contains('entry-template') && 
                    entry.style.display !== 'none'
        );
        
        return entries.map(entry => {
            const inputs = entry.querySelectorAll('input, textarea');
            return Array.from(inputs).map(input => input.value);
        });
    }

    handlePhotoUpload(file) {
        if (!file || !this.fotoPreview) return;
        
        // Validar tipo de arquivo
        if (!file.type.startsWith('image/')) {
            this.showAlert('Por favor, selecione apenas arquivos de imagem.', 'warning');
            return;
        }
        
        // Validar tamanho (máximo 2MB)
        if (file.size > 2 * 1024 * 1024) {
            this.showAlert('A imagem deve ter no máximo 2MB.', 'warning');
            return;
        }

        const reader = new FileReader();
        reader.onload = (e) => {
            this.fotoPreview.src = e.target.result;
            this.fotoPreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }

    async fetchCPFs() {
        try {
            const response = await fetch('php/cpfs.php');
            if (!response.ok) {
                throw new Error('Erro ao buscar CPFs');
            }
            return await response.json();
        } catch (error) {
            console.error('Erro ao buscar CPFs:', error);
            throw error;
        }
    }

    populateCPFModal(cpfs) {
        const select = document.getElementById('cpfList');
        if (!select) return;
        
        // Limpar opções anteriores
        select.innerHTML = '';
        
        // Adicionar opções
        cpfs.forEach(item => {
            const option = document.createElement('option');
            option.value = item.cpf;
            option.textContent = `${item.cpf_formatado} - ${item.nome}`;
            select.appendChild(option);
        });
    }

    showAlert(message, type = 'info') {
        if (!this.alert) return;
        
        this.alert.innerHTML = message;
        this.alert.className = `alert alert-${type}`;
        this.alert.style.display = 'block';
        
        // Auto-ocultar após alguns segundos
        setTimeout(() => {
            this.alert.style.display = 'none';
        }, 5000);
        
        // Rolar para o topo onde está o alerta
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }

    resetForm() {
        // Limpar formulário
        this.form.reset();
        
        // Ocultar preview da foto
        if (this.fotoPreview) {
            this.fotoPreview.style.display = 'none';
            this.fotoPreview.src = '';
        }
        
        // Remover seções dinâmicas extras, deixando apenas os templates
        document.querySelectorAll('.entries-container').forEach(container => {
            // Preservar o template
            const template = container.querySelector('.entry-template');
            
            // Limpar o container
            container.innerHTML = '';
            
            // Readicionar o template
            if (template) {
                container.appendChild(template);
            }
            
            // Adicionar um elemento inicial para cada seção
            this.cloneSection(container.id);
        });
    }
}

// Inicialização quando o DOM estiver carregado
document.addEventListener('DOMContentLoaded', () => {
    new CurriculoApp();
});