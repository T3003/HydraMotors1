<?php
// Coloque este bloco no topo do arquivo, antes de qualquer saída (antes de <!DOCTYPE html>)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (isset($_SESSION['logid'])) {
    header('Location: /CRUDTCC/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuario</title>
    <link href="/CRUDTCC/css/style.css" rel="stylesheet">
    <link href="/CRUDTCC/css/header.css" rel="stylesheet">
    <link href="/CRUDTCC/css/crudpf.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg,rgba(35, 37, 38, 0.14) 0%, #414345 100%);
            min-height: 100vh;
            margin: 0;
            font-family: 'Segoe UI', Arial, sans-serif;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 90vh;
        }
        .card {
            background: #222;
            border-radius: 18px;
            box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
            padding: 36px 32px 28px 32px;
            max-width: 420px;
            width: 100%;
            margin: 0 auto;
            
        }
        .card-header {
            background: transparent !important;
            color: #fff;
            text-align: center;
            border-bottom: none;
            margin-bottom: 18px;
        }
        .card-header h1 {
            font-size: 2.2rem;
            margin-bottom: 0.5rem;
            font-weight: 700;
        }
        .card-header small {
            color: #bbb;
            font-size: 1rem;
        }
        .form-label {
            color: #fff;
            font-weight: 500;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border-radius: 6px;
            border: 1px solid #444;
            background: #2c2c2c;
            color: #fff;
            margin-bottom: 16px;
            font-size: 1rem;
        }
        .form-control:focus {
            border-color: #007bff;
            outline: none;
            background: #232526;
        }
        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            color: #fff;
            border: none;
            border-radius: 6px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #0056b3 0%, #007bff 100%);
        }
        .btn-login-small {
            display: inline-block;
            padding: 7px 22px;
            background: linear-gradient(90deg, #007bff 0%, #0056b3 100%);
            color: #fff !important;
            border: none;
            border-radius: 6px;
            font-size: 0.98rem;
            font-weight: 600;
            cursor: pointer;
            margin-left: 8px;
            margin-top: 0;
            text-decoration: none;
            transition: background 0.2s;
        }
        .btn-login-small:hover {
            background: linear-gradient(90deg, #0056b3 0%, #007bff 100%);
            color: #fff !important;
            text-decoration: none;
        }
        .img-preview {
            display: block;
            margin: 10px auto 0 auto;
            max-width: 90px;
            max-height: 90px;
            border-radius: 50%;
            border: 2px solid #007bff;
            background: #fff;
        }
        .alert {
            margin-top: 18px;
        }
        @media (max-width: 600px) {
            .card {
                padding: 18px 8px 18px 8px;
                max-width: 98vw;
            }
            .card-header h1 {
                font-size: 1.3rem;
            }
        }
        body.cadastro-light {
            background: #eaf6fa !important;
            color: #222 !important;
        }
        .cadastro-container {
            background: #f5f4e6ee;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.10);
            color: #222;
        }
        .cadastro-container .card-header h1 {
            color: #1976d2;
        }
        .cadastro-container .form-label {
            color: #444;
        }
        .cadastro-container .form-control {
            background: #fff;
            color: #222;
            border: 1px solid #d0d0d0;
        }
        .cadastro-container .form-control:focus {
            border: 1.5px solid #1976d2;
            background: #f0f8ff;
        }
        .cadastro-container .btn-primary {
            background: #1976d2;
            color: #fff;
        }
        .cadastro-container .btn-primary:hover {
            background: #125ca1;
        }
        .cadastro-container .btn-login-small {
            background: #1976d2;
            color: #fff !important;
        }
        .cadastro-container .btn-login-small:hover {
            background: #125ca1;
        }
        .cadastro-container .img-preview {
            border: 2px solid #1976d2;
            background: #fff;
        }
    </style>
</head>
<body class="cadastro-light">
<?php
include_once("../../header.php"); 
?>
    <div class="container mt-4 cadastro-container">
        <div class="card mx-auto">
            <div class="card-header text-center bg-primary text-white">
                <h1>Cadastro de Usuário</h1>
                <small class="d-block mt-1">Campos com <span style="color:#d00">*</span> são obrigatórios.</small>
            </div>
            <div class="card-body">
                <form id="profileForm" action="enviar_codigo.php" method="POST" enctype="multipart/form-data" autocomplete="off">
                    <div class="mb-3">
                        <label for="nome" class="form-label required">Nome de Usuário</label>
                        <input type="text" id="nome" name="nome" class="form-control" required placeholder="Nome de Usuário">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required placeholder="Digite seu email">
                        <?php
                        // Exibe mensagem de limite de envio de e-mail, se houver
                        if (isset($_GET['erro']) && $_GET['erro'] === 'limite') {
                            $resta = isset($_GET['resta']) ? intval($_GET['resta']) : 0;
                            $min = floor($resta / 60);
                            $seg = $resta % 60;
                            echo '<div class="alert alert-warning text-center" style="margin-top:8px;">Aguarde ' . ($min > 0 ? $min . ' min ' : '') . $seg . ' seg para reenviar o e-mail de confirmação.</div>';
                        }
                        ?>
                    </div>
                    <div class="mb-3">
                        <label for="senha" class="form-label required">Senha do Usuário</label>
                        <input type="password" id="senha" name="password" class="form-control" required placeholder="Senha de pelo menos 8 caracteres">
                    </div>
                    <div class="mb-3">
                        <label for="confirmar_senha" class="form-label required">Confirmar Senha</label>
                        <input type="password" id="confirmar_senha" name="confirmar_senha" class="form-control" required placeholder="Repita a senha">
                        <input type="checkbox" id="showPass" style="margin-top:8px;"> <label for="showPass" style="font-size:0.98rem;cursor:pointer;">Mostrar Senha</label>
                    </div>
                    <div class="mb-3">
                        <label for="imagens" class="form-label">Imagem de perfil</label>
                        <input type="file" id="imagens" name="imagens[]" class="form-control" accept="image/*">
                        <img id="preview1" class="img-preview" style="display:none;">
                    </div>
                    <button type="submit" class="btn-primary">Cadastrar</button>
                </form>
                <?php
                // Mensagem de erro para email já cadastrado
                if (isset($_GET['erro']) && $_GET['erro'] === 'email') {
                    echo '<div class="alert alert-danger text-center" style="margin: 18px auto 0 auto; max-width: 400px;">Este email já está cadastrado. Por favor, utilize outro.</div>';
                }
                ?>
                <div style="margin-top:18px;text-align:center;">
                    <a> Já possui conta? </a>
                    <a href="login.php" class="btn-login-small">LOGIN</a>
                </div>
            </div>
        </div>
    </div>
    <script>
        // Mostrar/ocultar senha e confirmar senha
        document.getElementById('showPass').addEventListener('change', function() {
            var senha = document.getElementById('senha');
            var confirmar = document.getElementById('confirmar_senha');
            senha.type = this.checked ? 'text' : 'password';
            confirmar.type = this.checked ? 'text' : 'password';
        });
        // Preview da imagem
        document.getElementById('imagens').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.getElementById('preview1');
            if (file) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                preview.src = '';
                preview.style.display = 'none';
            }
        });
    </script>
</body>
</html>