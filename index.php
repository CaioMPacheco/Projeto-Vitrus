<?php include 'php/config.php'; ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Currículos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-file-alt"></i> Cadastro de Currículo</h1>

        <div id="alert" class="alert"></div>

        <form id="curriculoForm" class="form-curriculo" enctype="multipart/form-data">
            <!-- Seção Dados Pessoais -->
            <section class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-user"></i> Dados Pessoais</h2>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="required">Nome Completo</label>
                        <input type="text" name="nome" id="nome" required>
                    </div>

                    <div class="form-group">
                        <label class="required">CPF</label>
                        <input type="text" name="cpf" id="cpf" placeholder="000.000.000-00" maxlength="14" required>
                    </div>

                    <div class="form-group">
                        <label class="required">Telefone</label>
                        <input type="text" name="telefone" id="telefone" placeholder="(00) 00000-0000" maxlength="15" required>
                    </div>

                    <div class="form-group">
                        <label class="required">Cidade</label>
                        <input type="text" name="cidade" id="cidade" required>
                    </div>

                    <div class="form-group">
                        <label class="required">Habilitado</label>
                        <select name="habilitado" id="habilitado" required>
                            <option value="sim">Sim</option>
                            <option value="nao">Não</option>
                        </select>
                    </div>

                    <div class="form-group file-upload">
                        <label>Foto de Perfil</label>
                        <div class="upload-container">
                            <input type="file" name="foto" id="foto" accept="image/*">
                            <label for="foto" class="upload-btn">
                                <i class="fas fa-camera"></i>
                                <span>Selecionar Foto</span>
                            </label>
                            <img id="fotoPreview" class="preview" alt="Prévia da foto">
                        </div>
                    </div>
                </div>
            </section>

            <!-- Seção Formação -->
            <section class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-graduation-cap"></i> Formação Acadêmica</h2>
                    <button type="button" id="addFormacao" class="btn btn-add">
                        <i class="fas fa-plus"></i> Adicionar
                    </button>
                </div>
                
                <div id="formacoesContainer" class="entries-container">
                    <div class="entry-template form-entry">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="required">Curso</label>
                                <input type="text" name="formacao[]" value="Ex: T.I" disabled required>
                            </div>
                            
                            <div class="form-group">
                                <label>Instituição</label>
                                <input type="text" name="instituicao[]" value="Ex: SENAI" disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Ano de Conclusão</label>
                                <input type="number" name="ano[]" value="Ex: 2011" disabled min="1900" max="2100">
                            </div>
                            
                            <button type="button" class="btn btn-remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Seção Experiência -->
            <section class="form-section">
                <div class="section-header">
                    <h2><i class="fas fa-briefcase"></i> Experiência Profissional</h2>
                    <button type="button" id="addExperiencia" class="btn btn-add">
                        <i class="fas fa-plus"></i> Adicionar
                    </button>
                </div>
                
                <div id="experienciasContainer" class="entries-container">
                    <div class="entry-template form-entry">
                        <div class="form-grid">
                            <div class="form-group">
                                <label class="required">Cargo</label>
                                <input type="text" name="cargo[]" value="Ex: Desenvolvedor de software" disabled required>
                            </div>
                            
                            <div class="form-group">
                                <label class="required">Empresa</label>
                                <input type="text" name="empresa[]" value="Ex: AMBEV" disabled required>
                            </div>
                            
                            <div class="form-group">
                                <label>Data Início</label>
                                <input type="date" name="inicio[]"  disabled>
                            </div>
                            
                            <div class="form-group">
                                <label>Data Fim</label>
                                <input type="date" name="fim[]"  disabled>
                            </div>
                            
                            <div class="form-group full-width">
                                <label>Descrição</label>
                                <textarea name="descricao[]" value="Ex: Trabalhei com PHP e Laravel" disabled rows="2"></textarea>
                            </div>
                            
                            <button type="button" class="btn btn-remove">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Currículo
                </button>
                <button type="button" id="gerarPDF" class="btn btn-secondary">
                    <i class="fas fa-file-pdf"></i> Gerar PDF
                </button>
            </div>
        </form>

        <!-- Modal -->
        <div id="pdfModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h3><i class="fas fa-file-pdf"></i> Selecione um CPF</h3>
                <select id="cpfList"></select>
                <button id="confirmarCPF" class="btn btn-primary">
                    <i class="fas fa-download"></i> Gerar PDF
                </button>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="assets/js/script.js"></script>
</body>
</html>