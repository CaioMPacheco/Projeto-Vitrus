<?php
/**
 * Salvar currículo
 * 
 * Este script recebe os dados do formulário via JSON
 * e salva no banco de dados
 */
require 'config.php';

header('Content-Type: application/json');

try {
    // Obter dados do formulário
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Formato JSON inválido");
    }

    // Sanitização e validação básica
    $required = ['nome', 'cpf', 'telefone', 'cidade', 'habilitado'];
    foreach ($required as $campo) {
        if (empty($data[$campo])) {
            throw new Exception("Campo obrigatório faltando: $campo");
        }
    }

    // Validar CPF
    $cpf = preg_replace('/[^0-9]/', '', $data['cpf']);
    if (!validarCPF($cpf)) {
        throw new Exception("CPF inválido");
    }
    
    // Verificar se CPF já existe
    $checkCpf = $pdo->prepare("SELECT id FROM candidatos WHERE cpf = ?");
    $checkCpf->execute([$cpf]);
    if ($checkCpf->rowCount() > 0) {
        throw new Exception("Este CPF já está cadastrado no sistema");
    }

    // Processar foto
    $foto = null;
    $fotoTipo = null;
    if (!empty($data['foto']['data']) && !empty($data['foto']['type'])) {
        if (strpos($data['foto']['type'], 'image/') === 0) {
            $foto = base64_decode($data['foto']['data']);
            $fotoTipo = $data['foto']['type'];
        } else {
            throw new Exception("Tipo de arquivo de imagem inválido");
        }
    }

    // Iniciar transação
    $pdo->beginTransaction();

    // Inserir candidato
    $stmt = $pdo->prepare("INSERT INTO candidatos 
        (nome, cpf, telefone, cidade, habilitado, foto, foto_tipo)
        VALUES (?, ?, ?, ?, ?, ?, ?)");

    $stmt->execute([
        sanitizar($data['nome']),
        $cpf,
        preg_replace('/[^0-9]/', '', $data['telefone']),
        sanitizar($data['cidade']),
        $data['habilitado'] === 'sim' ? 'sim' : 'nao',
        $foto,
        $fotoTipo
    ]);

    $candidatoId = $pdo->lastInsertId();

    // Inserir formações
    if (!empty($data['formacoes'])) {
        $stmtForm = $pdo->prepare("INSERT INTO formacoes 
            (candidato_id, descricao, instituicao, ano)
            VALUES (?, ?, ?, ?)");

        foreach ($data['formacoes'] as $formacao) {
            if (empty($formacao[0])) continue;
            
            $stmtForm->execute([
                $candidatoId,
                sanitizar($formacao[0] ?? ''),
                sanitizar($formacao[1] ?? ''),
                !empty($formacao[2]) ? intval($formacao[2]) : null
            ]);
        }
    }

    // Inserir experiências
    if (!empty($data['experiencias'])) {
        $stmtExp = $pdo->prepare("INSERT INTO experiencias 
            (candidato_id, cargo, empresa, inicio, fim, descricao)
            VALUES (?, ?, ?, ?, ?, ?)");

        foreach ($data['experiencias'] as $exp) {
            if (empty($exp[0]) || empty($exp[1])) continue;
            
            // Validar datas
            $dataInicio = !empty($exp[2]) ? date('Y-m-d', strtotime($exp[2])) : null;
            $dataFim = !empty($exp[3]) ? date('Y-m-d', strtotime($exp[3])) : null;
            
            $stmtExp->execute([
                $candidatoId,
                sanitizar($exp[0]),
                sanitizar($exp[1]),
                $dataInicio,
                $dataFim,
                sanitizar($exp[4] ?? '')
            ]);
        }
    }

    // Confirmar transação
    $pdo->commit();
    
    echo json_encode([
        'success' => true, 
        'id' => $candidatoId,
        'message' => 'Currículo cadastrado com sucesso!'
    ]);

} catch (PDOException $e) {
    // Reverter transação em caso de erro
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao salvar no banco de dados']);
    error_log('Erro PDO: ' . $e->getMessage());
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
?>