<?php
/**
 * Buscar currículo por CPF
 * 
 * Este script recebe um CPF via GET e retorna os dados
 * do currículo correspondente em formato JSON
 */
require 'config.php';

header('Content-Type: application/json');

try {
    // Validar CPF
    $cpf = preg_replace('/[^0-9]/', '', $_GET['cpf'] ?? '');
    
    if (strlen($cpf) !== 11 || !validarCPF($cpf)) {
        throw new Exception("CPF inválido");
    }

    // Buscar dados do candidato
    $stmt = $pdo->prepare("SELECT 
        id, nome, cpf, telefone, cidade, habilitado,
        TO_BASE64(foto) as foto, foto_tipo
        FROM candidatos WHERE cpf = ?");
    $stmt->execute([$cpf]);
    $candidato = $stmt->fetch();

    if (!$candidato) {
        throw new Exception("Candidato não encontrado");
    }

    // Formatar CPF e telefone para exibição
    $candidato['cpf_formatado'] = formatarCPF($candidato['cpf']);
    $candidato['telefone_formatado'] = formatarTelefone($candidato['telefone']);

    // Buscar formações
    $stmtForm = $pdo->prepare("SELECT * FROM formacoes WHERE candidato_id = ? ORDER BY ano DESC");
    $stmtForm->execute([$candidato['id']]);
    $candidato['formacoes'] = $stmtForm->fetchAll();

    // Buscar experiências
    $stmtExp = $pdo->prepare("SELECT * FROM experiencias WHERE candidato_id = ? ORDER BY inicio DESC");
    $stmtExp->execute([$candidato['id']]);
    $experiencias = $stmtExp->fetchAll();
    
    // Formatar datas das experiências
    foreach ($experiencias as &$exp) {
        if (!empty($exp['inicio'])) {
            $exp['inicio_formatado'] = date('d/m/Y', strtotime($exp['inicio']));
        }
        if (!empty($exp['fim'])) {
            $exp['fim_formatado'] = date('d/m/Y', strtotime($exp['fim']));
        } else {
            $exp['fim_formatado'] = 'Atual';
        }
    }
    $candidato['experiencias'] = $experiencias;

    echo json_encode($candidato);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>