<?php
/**
 * Listar CPFs
 * 
 * Este script retorna todos os CPFs cadastrados
 * no sistema para seleção na geração de PDF
 */
require 'config.php';

header('Content-Type: application/json');

try {
    // Buscar todos os CPFs e nomes para exibição
    $stmt = $pdo->query("
        SELECT 
            id, 
            cpf, 
            nome 
        FROM candidatos 
        ORDER BY nome ASC
    ");
    
    $resultado = $stmt->fetchAll();
    
    // Formatar CPFs para exibição
    $dados = [];
    foreach ($resultado as $row) {
        $dados[] = [
            'id' => $row['id'],
            'cpf' => $row['cpf'],
            'cpf_formatado' => formatarCPF($row['cpf']),
            'nome' => $row['nome']
        ];
    }
    
    echo json_encode($dados);
    
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao buscar CPFs']);
    error_log('Erro ao buscar CPFs: ' . $e->getMessage());
}
?>