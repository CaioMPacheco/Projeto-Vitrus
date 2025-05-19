<?php
/**
 * Gerar PDF do currículo
 * 
 * Este script recebe um CPF via GET e gera um PDF
 * com os dados do currículo correspondente
 */
require_once('../vendor/autoload.php'); // Certifique-se que TCPDF está instalado via Composer
require_once('config.php');

use TCPDF as PDF;

class CurriculoPDF extends PDF {
    // Cores do tema
    private $corPrimaria = [41, 128, 185]; // Azul
    private $corSecundaria = [52, 73, 94]; // Cinza escuro
    private $corTitulos = [41, 128, 185]; // Azul
    private $corSubtitulos = [52, 73, 94]; // Cinza escuro
    private $corTexto = [44, 62, 80]; // Cinza escuro para texto
    private $corDivisor = [189, 195, 199]; // Cinza claro para linhas

    // Cabeçalho personalizado
    public function Header() {
        // Logo ou imagem do cabeçalho (opcional)
        $this->SetY(10);
        
        // Título do documento
        $this->SetFont('helvetica', 'B', 20);
        $this->SetTextColor($this->corPrimaria[0], $this->corPrimaria[1], $this->corPrimaria[2]);
        $this->Cell(0, 10, 'CURRÍCULO PROFISSIONAL', 0, 1, 'C');
        
        // Linha decorativa
        $this->SetLineWidth(0.5);
        $this->SetDrawColor($this->corPrimaria[0], $this->corPrimaria[1], $this->corPrimaria[2]);
        $this->Line(15, 22, 195, 22);
        
        // Segunda linha decorativa mais fina
        $this->SetLineWidth(0.2);
        $this->SetDrawColor($this->corSecundaria[0], $this->corSecundaria[1], $this->corSecundaria[2]);
        $this->Line(25, 24, 185, 24);
    }

    // Rodapé personalizado
    public function Footer() {
        $this->SetY(-15);
        
        // Linha separadora
        $this->SetLineWidth(0.2);
        $this->SetDrawColor($this->corDivisor[0], $this->corDivisor[1], $this->corDivisor[2]);
        $this->Line(15, 282, 195, 282);
        
        // Informações do rodapé
        $this->SetFont('helvetica', 'I', 8);
        $this->SetTextColor($this->corSecundaria[0], $this->corSecundaria[1], $this->corSecundaria[2]);
        $this->Cell(0, 5, 'Documento gerado em ' . date('d/m/Y H:i'), 0, 0, 'L');
        $this->Cell(0, 5, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }
    
    // Função auxiliar para criar títulos de seções
    public function addSectionTitle($title, $icon = '') {
        // Espaçamento antes do título
        $this->Ln(8);
        
        // Definir cor do título
        $this->SetFont('helvetica', 'B', 14);
        $this->SetTextColor($this->corTitulos[0], $this->corTitulos[1], $this->corTitulos[2]);
        
        // Ícone + Título
        $titleText = $icon ? $icon . ' ' . $title : $title;
        $this->Cell(0, 10, $titleText, 0, 1, 'L');
        
        // Linha abaixo do título
        $this->SetLineWidth(0.2);
        $this->SetDrawColor($this->corPrimaria[0], $this->corPrimaria[1], $this->corPrimaria[2]);
        $this->Line(15, $this->GetY(), 195, $this->GetY());
        
        // Restaurar cor do texto para continuar
        $this->SetTextColor($this->corTexto[0], $this->corTexto[1], $this->corTexto[2]);
        $this->Ln(2);
    }
    
    // Função para adicionar entry com título em negrito e conteúdo
    public function addInfoItem($label, $value, $bold = true) {
        if ($bold) {
            $this->SetFont('helvetica', 'B', 10);
            $this->Cell(35, 6, $label . ':', 0, 0);
            $this->SetFont('helvetica', '', 10);
            $this->Cell(0, 6, $value, 0, 1);
        } else {
            $this->SetFont('helvetica', '', 10);
            $this->Cell(35, 6, $label . ':', 0, 0);
            $this->Cell(0, 6, $value, 0, 1);
        }
    }
}

try {
    // Validar CPF
    $cpf = preg_replace('/[^0-9]/', '', $_GET['cpf'] ?? '');
    if (strlen($cpf) !== 11 || !validarCPF($cpf)) {
        throw new Exception('CPF inválido');
    }

    // Buscar candidato
    $stmt = $pdo->prepare("
        SELECT * FROM candidatos 
        WHERE cpf = ?
    ");
    $stmt->execute([$cpf]);
    $candidato = $stmt->fetch();

    if (!$candidato) {
        throw new Exception('Candidato não encontrado');
    }

    // Buscar formações
    $stmtForm = $pdo->prepare("
        SELECT * FROM formacoes 
        WHERE candidato_id = ? 
        ORDER BY ano DESC
    ");
    $stmtForm->execute([$candidato['id']]);
    $formacoes = $stmtForm->fetchAll();

    // Buscar experiências
    $stmtExp = $pdo->prepare("
        SELECT * FROM experiencias 
        WHERE candidato_id = ? 
        ORDER BY inicio DESC
    ");
    $stmtExp->execute([$candidato['id']]);
    $experiencias = $stmtExp->fetchAll();

    // Configurar PDF
    $pdf = new CurriculoPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetCreator('Sistema de Currículos');
    $pdf->SetAuthor($candidato['nome']);
    $pdf->SetTitle('Currículo - ' . $candidato['nome']);
    $pdf->SetMargins(15, 30, 15);
    $pdf->SetAutoPageBreak(true, 25);
    $pdf->SetFillColor(249, 249, 249);
    $pdf->AddPage();

    // Definir texto padrão em cinza escuro
    $pdf->SetTextColor(44, 62, 80);

    // Nome do candidato em destaque
    $pdf->SetFont('helvetica', 'B', 16);
    $pdf->Cell(0, 12, mb_strtoupper($candidato['nome'], 'UTF-8'), 0, 1, 'C');
    $pdf->Ln(2);

    // Foto do candidato (se existir)
    if (!empty($candidato['foto'])) {
        $tmpFile = tempnam(sys_get_temp_dir(), 'curriculo');
        file_put_contents($tmpFile, $candidato['foto']);
        
        // Desenhar borda circular (ou retangular com bordas arredondadas)
        $fotoX = 160;
        $fotoY = 30;
        $fotoW = 20;
        $fotoH = 18;
        
        // Imagem com bordas
        $pdf->Image($tmpFile, $fotoX, $fotoY, $fotoW, $fotoH, '', '', 'T', false, 300, '', false, false, 1, true);
        
        // Borda da imagem
        $pdf->SetLineWidth(0.5);
        $pdf->SetDrawColor(41, 128, 185); // Cor primária
        $pdf->RoundedRect($fotoX, $fotoY, $fotoW, $fotoH, 2, '1111', 'D');
        
        unlink($tmpFile);
    }

    // Dados pessoais
    $pdf->addSectionTitle('DADOS PESSOAIS', 'ℹ️');
    
    // Cria um fundo cinza claro para destacar os dados pessoais
    $startY = $pdf->GetY();
    $pdf->Rect(15, $startY, 180, 28, 'F', [], [249, 249, 249]);
    
    // Layout em colunas para os dados pessoais
    $pdf->SetFont('helvetica', '', 10);
    
    // Coluna 1
    $pdf->SetXY(18, $startY + 3);
    $pdf->addInfoItem('CPF', formatarCPF($candidato['cpf']));
    $pdf->SetX(18);
    $pdf->addInfoItem('Telefone', formatarTelefone($candidato['telefone']));
    
    // Coluna 2 
    $pdf->SetXY(110, $startY + 3);
    $pdf->addInfoItem('Cidade', $candidato['cidade']);
    $pdf->SetX(110);
    $pdf->addInfoItem('Habilitado', ($candidato['habilitado'] === 'sim' ? 'Sim' : 'Não'));
    
    $pdf->Ln(8);

    // Formações
    $pdf->addSectionTitle('FORMAÇÃO ACADÊMICA');
    
    if (empty($formacoes)) {
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 6, 'Nenhuma formação acadêmica cadastrada', 0, 1);
    } else {
        $alternarFundo = true;
        
        foreach ($formacoes as $index => $formacao) {
            // Alternar fundo para melhor legibilidade
            $startY = $pdf->GetY();
            $altura = 16; // Altura estimada, ajustar conforme necessário
            
            if ($alternarFundo) {
                $pdf->Rect(15, $startY, 180, $altura, 'F', [], [249, 249, 249]);
            }
            $alternarFundo = !$alternarFundo;
            
            // Círculo numerado
            $pdf->SetFillColor(41, 128, 185);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetXY(18, $startY + 2);
            $pdf->Cell(5, 5, ($index + 1), 0, 0, 'C', true);
            $pdf->SetTextColor(44, 62, 80);
            
            // Título da formação
            $pdf->SetXY(25, $startY + 2);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(0, 5, $formacao['descricao'], 0, 1);
            
            // Detalhes da formação
            $pdf->SetFont('helvetica', '', 10);
            $detalhes = [];
            
            if (!empty($formacao['instituicao'])) {
                $detalhes[] = 'Instituição: ' . $formacao['instituicao'];
            }
            
            if (!empty($formacao['ano'])) {
                $detalhes[] = 'Conclusão: ' . $formacao['ano'];
            }
            
            $pdf->SetX(25);
            $pdf->Cell(0, 5, implode(' | ', $detalhes), 0, 1);
            
            $pdf->Ln(2);
        }
    }

    // Experiências
    if ($pdf->GetY() > 200) {
        $pdf->AddPage();
    } else {
        $pdf->Ln(5);
    }
    
    $pdf->addSectionTitle('EXPERIÊNCIA PROFISSIONAL');

    if (empty($experiencias)) {
        $pdf->SetFont('helvetica', 'I', 10);
        $pdf->Cell(0, 6, 'Nenhuma experiência profissional cadastrada', 0, 1);
    } else {
        $alternarFundo = true;
        
        foreach ($experiencias as $index => $exp) {
            // Altura estimada com base na descrição (se tiver)
            $descHeight = !empty($exp['descricao']) ? 
                ceil(strlen($exp['descricao']) / 100) * 6 + 6 : 0;
            $altura = 20 + $descHeight;
            
            $startY = $pdf->GetY();
            
            // Fundo alternado
            if ($alternarFundo) {
                $pdf->Rect(15, $startY, 180, $altura, 'F', [], [249, 249, 249]);
            }
            $alternarFundo = !$alternarFundo;
            
            // Círculo numerado
            $pdf->SetFillColor(41, 128, 185);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->SetXY(18, $startY + 2);
            $pdf->Cell(5, 5, ($index + 1), 0, 0, 'C', true);
            $pdf->SetTextColor(44, 62, 80);
            
            // Cargo em destaque
            $pdf->SetXY(25, $startY + 2);
            $pdf->SetFont('helvetica', 'B', 11);
            $pdf->Cell(0, 5, $exp['cargo'], 0, 1);
            
            // Empresa
            $pdf->SetX(25);
            $pdf->SetFont('helvetica', 'B', 10);
            $pdf->Cell(0, 5, $exp['empresa'], 0, 1);
            
            // Período
            $periodo = '';
            if (!empty($exp['inicio'])) {
                $periodo = 'De ' . date('m/Y', strtotime($exp['inicio']));
                $periodo .= !empty($exp['fim']) ? ' até ' . date('m/Y', strtotime($exp['fim'])) : ' até o momento';
                
                $pdf->SetX(25);
                $pdf->SetFont('helvetica', 'I', 9);
                $pdf->Cell(0, 5, $periodo, 0, 1);
            }
            
            // Descrição
            if (!empty($exp['descricao'])) {
                $pdf->SetX(25);
                $pdf->SetFont('helvetica', '', 10);
                $pdf->MultiCell(0, 5, $exp['descricao'], 0, 'L');
            }
            
            $pdf->Ln(4);
            
            // Adicionar nova página se necessário
            if ($pdf->GetY() > 270 && $index < count($experiencias) - 1) {
                $pdf->AddPage();
            }
        }
    }

    // Saída do PDF
    $pdf->Output('curriculo_' . $cpf . '.pdf', 'D');

} catch (Exception $e) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}