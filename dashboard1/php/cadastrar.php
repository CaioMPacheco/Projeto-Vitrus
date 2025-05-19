<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Configuração do banco de dados
    $servername = "localhost";
    $username = "seu_usuario";
    $password = "sua_senha";
    $dbname = "assistec";
    
    try {
        $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8", $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Coletar e validar dados
        $dados = [
            'nome' => trim($_POST['nome']),
            'email' => filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL),
            'senha' => password_hash($_POST['senha'], PASSWORD_DEFAULT),
            'telefone' => preg_replace('/[^0-9]/', '', $_POST['telefone']),
            'rua' => trim($_POST['rua']),
            'numero' => trim($_POST['numero']),
            'complemento' => trim($_POST['complemento']),
            'bairro' => trim($_POST['bairro']),
            'cidade' => trim($_POST['cidade']),
            'estado' => trim($_POST['estado']),
            'cep' => preg_replace('/[^0-9]/', '', $_POST['cep'])
        ];

        if (!$dados['email']) {
            throw new Exception("Email inválido");
        }

        // Verificar se email já existe
        $stmt = $conn->prepare("SELECT id_usuario FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $dados['email']);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            throw new Exception("Este email já está cadastrado");
        }

        // Inserir no banco de dados
        $sql = "INSERT INTO usuarios (
            nome, email, senha, telefone, rua, numero, complemento, 
            bairro, cidade, estado, cep, nivel_usuario, usuario_ativo, tema
        ) VALUES (
            :nome, :email, :senha, :telefone, :rua, :numero, 
            :complemento, :bairro, :cidade, :estado, :cep, 
            'Cliente', 'sim', 'light'
        )";

        $stmt = $conn->prepare($sql);
        $stmt->execute($dados);

        // Configurar e enviar email
        $mail = new PHPMailer(true);

        // Configurações SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.seuprovedor.com';  // Ex: smtp.hostinger.com
        $mail->SMTPAuth = true;
        $mail->Username = 'no-reply@assistec.com.br';
        $mail->Password = 'sua_senha_email';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->Port = 465;
        $mail->CharSet = 'UTF-8';

        // Remetente e destinatário
        $mail->setFrom('no-reply@assistec.com.br', 'Assistec');
        $mail->addAddress($dados['email'], $dados['nome']);

        // Conteúdo do email
        $mail->isHTML(true);
        $mail->Subject = 'Cadastro realizado com sucesso!';

        $mail->Body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 20px auto; padding: 20px; border: 1px solid #ddd; }
                    .header { text-align: center; padding: 10px; background-color: #f8f9fa; }
                    .logo { font-size: 24px; color: #2c3e50; font-weight: bold; }
                    .content { padding: 20px; }
                    .destaque { color: #e67e22; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <div class='logo'>Assistec</div>
                        <h2>Bem-vindo à nossa plataforma!</h2>
                    </div>
                    
                    <div class='content'>
                        <p>Olá <span class='destaque'>{$dados['nome']}</span>,</p>
                        <p>Seu cadastro foi realizado com sucesso em nosso sistema.</p>
                        
                        <h3>Seus dados de acesso:</h3>
                        <ul>
                            <li><strong>Email:</strong> {$dados['email']}</li>
                            <li><strong>Telefone:</strong> {$dados['telefone']}</li>
                            <li><strong>Endereço:</strong> {$dados['rua']}, {$dados['numero']} - {$dados['bairro']}</li>
                        </ul>
                        
                        <p>Acesse nossa loja: <a href='https://www.assistec.com.br/loja'>https://www.assistec.com.br/loja</a></p>
                        
                        <p>Atenciosamente,<br>
                        Equipe Assistec</p>
                    </div>
                </div>
            </body>
            </html>
        ";

        $mail->send();
        header("Location: ../login.html?cadastro=sucesso");
        
    } catch (PDOException $e) {
        error_log("Erro no banco de dados: " . $e->getMessage());
        header("Location: ../cadastro.html?erro=db");
    } catch (Exception $e) {
        error_log("Erro geral: " . $e->getMessage());
        header("Location: ../cadastro.html?erro=" . urlencode($e->getMessage()));
    } finally {
        if (isset($conn)) {
            $conn = null;
        }
    }
} else {
    header("Location: ../cadastro.html");
}
?>