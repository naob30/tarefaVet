<?php
include("conn.php");

// Verificar se está editando um cliente
$edit = false;
$nomeCliente = $telefoneCliente = $emailCliente = '';
if (isset($_GET['edit'])) {
    $edit = true;
    $codCliente = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT c.*, a.nomeAnimal, a.fotoAnimal, t.tipoAnimal 
                           FROM tbCliente c 
                           LEFT JOIN tbAnimal a ON c.codCliente = a.codCliente 
                           LEFT JOIN tbTipoAnimal t ON a.codTipoAnimal = t.codTipoAnimal 
                           WHERE c.codCliente = ?');
    $stmt->execute([$codCliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomeCliente = $cliente['nomeCliente'];
    $telefoneCliente = $cliente['telefoneCliente'];
    $emailCliente = $cliente['EmailCliente'];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeCliente = $_POST['nomeCliente'];
    $telefoneCliente = $_POST['telefoneCliente'];
    $emailCliente = $_POST['emailCliente'];

    if ($edit) {
        // Atualizar cliente
        $stmt = $pdo->prepare('UPDATE tbCliente SET nomeCliente = ?, telefoneCliente = ?, EmailCliente = ? WHERE codCliente = ?');
        $stmt->execute([$nomeCliente, $telefoneCliente, $emailCliente, $codCliente]);
    } else {
        // Inserir novo cliente
        $stmt = $pdo->prepare('INSERT INTO tbCliente (nomeCliente, telefoneCliente, EmailCliente) VALUES (?, ?, ?)');
        $stmt->execute([$nomeCliente, $telefoneCliente, $emailCliente]);
    }

    header('Location: addCliente.php?success=1');
    exit();
}

// Excluir cliente
if (isset($_GET['delete'])) {
    $codCliente = $_GET['delete'];
    // Primeiro, excluir todos os registros relacionados na tabela tbAnimal
    $stmt = $pdo->prepare('DELETE FROM tbAnimal WHERE codCliente = ?');
    $stmt->execute([$codCliente]);

    // Agora, excluir o cliente
    $stmt = $pdo->prepare('DELETE FROM tbCliente WHERE codCliente = ?');
    $stmt->execute([$codCliente]);
    header('Location: addCliente.php');
    exit();
}

// Buscar todos os clientes (OS JOIN ESTÃO AQUI)
$clientes = $pdo->query('SELECT c.*, a.nomeAnimal, a.fotoAnimal, t.tipoAnimal 
                         FROM tbCliente c 
                         LEFT JOIN tbAnimal a ON c.codCliente = a.codCliente 
                         LEFT JOIN tbTipoAnimal t ON a.codTipoAnimal = t.codTipoAnimal')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente</title>
    <link rel="shortcut icon" href="img/icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <div class="logo">
            <img src="img/icon.png" alt="">
            <h3 class="text-uppercase">Clínica Veterinária</h3>
        </div>
        <div class="atendimento">
            <a href="index.php"><i style="margin-right: 10px;" class="bi bi-house"></i>VOLTAR PARA PÁGINA PRINCIPAL</a>
        </div>
    </header>

    <div class="banner imgCliente">
        <img class="imgCliente" src="img/client.png" alt="">
        <div class="cadastro">
            <div class="card">
                <div class="card-header font-monospace"><?php echo $edit ? 'EDITAR CLIENTE' : 'CADASTRAR CLIENTE'; ?></div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])) : ?>
                        <div class="success-message" id="success-message">
                            CLIENTE <?php echo $edit ? 'EDITADO' : 'CADASTRADO'; ?> COM SUCESSO!
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Digite o nome do cliente..." aria-label="Nome" aria-describedby="addon-wrapping" name="nomeCliente" value="<?php echo htmlspecialchars($nomeCliente); ?>" required>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Digite o telefone do cliente..." aria-label="Telefone" aria-describedby="addon-wrapping" name="telefoneCliente" value="<?php echo htmlspecialchars($telefoneCliente); ?>" required>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" class="form-control font-monospace" placeholder="Digite o e-mail do cliente..." aria-label="Email" aria-describedby="addon-wrapping" name="emailCliente" value="<?php echo htmlspecialchars($emailCliente); ?>" required>
                        </div>

                        <input type="submit" value="<?php echo $edit ? 'EDITAR CLIENTE' : 'CADASTRAR CLIENTE'; ?>" class="btn btn-primary">
                    </form>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Animal</th>
                            <th>Foto do Animal</th>
                            <th>Tipo do Animal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cliente['codCliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['nomeCliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefoneCliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['EmailCliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['nomeAnimal'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($cliente['fotoAnimal'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($cliente['tipoAnimal'] ?? ''); ?></td>
                                <td>
                                    <a href="addCliente.php?edit=<?php echo $cliente['codCliente']; ?>" class="edit-btn"><i style="margin-right: 10px;" class="bi bi-pencil-fill"></i></a>
                                    <a href="addCliente.php?delete=<?php echo $cliente['codCliente']; ?>" class="delete-btn"><i class="bi bi-trash-fill"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <h5 class="font-monospace" style="font-size: 15px; color: #008186;">&copy; 2023 Nayara Brabo. All rights reserved.</h5>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.display = 'block';
                setTimeout(() => {
                    successMessage.style.opacity = '1';
                    successMessage.style.transition = 'opacity 0.5s';
                }, 10);

                setTimeout(() => {
                    successMessage.style.opacity = '0';
                    successMessage.style.transition = 'opacity 0.5s';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>

</html>



































































































































































<!-- <?php
include("conn.php");

// Verificar se está editando um cliente
$edit = false;
$nomeCliente = $telefoneCliente = $emailCliente = '';
if (isset($_GET['edit'])) {
    $edit = true;
    $codCliente = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT c.*, a.nomeAnimal, a.fotoAnimal, t.tipoAnimal FROM tbCliente c 
                            JOIN tbAnimal a ON c.codCliente = a.codCliente 
                            JOIN tbTipoAnimal t ON a.codTipoAnimal = t.codTipoAnimal 
                            WHERE c.codCliente = ?');
    $stmt->execute([$codCliente]);
    $cliente = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomeCliente = $cliente['nomeCliente'];
    $telefoneCliente = $cliente['telefoneCliente'];
    $emailCliente = $cliente['EmailCliente'];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeCliente = $_POST['nomeCliente'];
    $telefoneCliente = $_POST['telefoneCliente'];
    $emailCliente = $_POST['emailCliente'];

    if ($edit) {
        // Atualizar cliente
        $stmt = $pdo->prepare('UPDATE tbCliente SET nomeCliente = ?, telefoneCliente = ?, EmailCliente = ? WHERE codCliente = ?');
        $stmt->execute([$nomeCliente, $telefoneCliente, $emailCliente, $codCliente]);
    } else {
        // Inserir novo cliente
        $stmt = $pdo->prepare('INSERT INTO tbCliente (nomeCliente, telefoneCliente, EmailCliente) VALUES (?, ?, ?)');
        $stmt->execute([$nomeCliente, $telefoneCliente, $emailCliente]);
        $codCliente = $pdo->lastInsertId(); // Obtém o ID do cliente recém-inserido
    }

    header('Location: addCliente.php?success=1');
    exit();
}

// Excluir cliente
if (isset($_GET['delete'])) {
    $codCliente = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tbCliente WHERE codCliente = ?');
    $stmt->execute([$codCliente]);
    header('Location: addCliente.php');
    exit();
}

// Buscar todos os clientes
$clientes = $pdo->query('SELECT c.*, a.nomeAnimal, a.fotoAnimal, t.tipoAnimal FROM tbCliente c 
                            JOIN tbAnimal a ON c.codCliente = a.codCliente 
                            JOIN tbTipoAnimal t ON a.codTipoAnimal = t.codTipoAnimal')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Cliente</title>
    <link rel="shortcut icon" href="img/icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
</head>

<body>
    <header>
        <div class="logo">
            <img src="img/icon.png" alt="">
            <h3 class="text-uppercase">Clínica Veterinária</h3>
        </div>
        <div class="atendimento">
            <a href="index.php"><i style="margin-right: 10px;" class="bi bi-house"></i>VOLTAR PARA PÁGINA PRINCIPAL</a>
        </div>
    </header>

    <div class="banner imgCliente">
        <img class="imgCliente" src="img/client.png" alt="">
        <div class="cadastro">
            <div class="card">
                <div class="card-header font-monospace"><?php echo $edit ? 'EDITAR CLIENTE' : 'CADASTRAR CLIENTE'; ?></div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])) : ?>
                        <div class="success-message" id="success-message">
                            CLIENTE <?php echo $edit ? 'EDITADO' : 'CADASTRADO'; ?> COM SUCESSO!
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Digite o nome do cliente..." aria-label="Nome" aria-describedby="addon-wrapping" name="nomeCliente" value="<?php echo htmlspecialchars($nomeCliente); ?>" required>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-telephone-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Digite o telefone do cliente..." aria-label="Telefone" aria-describedby="addon-wrapping" name="telefoneCliente" value="<?php echo htmlspecialchars($telefoneCliente); ?>" required>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-envelope-fill"></i></span>
                            <input type="email" class="form-control font-monospace" placeholder="Digite o e-mail do cliente..." aria-label="Email" aria-describedby="addon-wrapping" name="emailCliente" value="<?php echo htmlspecialchars($emailCliente); ?>" required>
                        </div>

                        <input type="submit" value="<?php echo $edit ? 'EDITAR CLIENTE' : 'CADASTRAR CLIENTE'; ?>" class="btn btn-primary">
                    </form>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Telefone</th>
                            <th>Email</th>
                            <th>Animal</th>
                            <th>Foto do Animal</th>
                            <th>Tipo do Animal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($clientes as $cliente) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($cliente['codCliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['nomeCliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['telefoneCliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['EmailCliente']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['nomeAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['fotoAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($cliente['tipoAnimal']); ?></td>
                                <td>
                                    <a href="addCliente.php?edit=<?php echo $cliente['codCliente']; ?>" class="edit-btn"><i style="margin-right: 10px;" class="bi bi-pencil-fill"></i></a>
                                    <a href="addCliente.php?delete=<?php echo $cliente['codCliente']; ?>" class="delete-btn"><i class="bi bi-trash-fill"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <h5 class="font-monospace" style="font-size: 15px; color: #008186;">&copy; 2023 Nayara Brabo. All rights reserved.</h5>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const successMessage = document.getElementById('success-message');
            if (successMessage) {
                successMessage.style.display = 'block';
                setTimeout(() => {
                    successMessage.style.opacity = '1';
                    successMessage.style.transition = 'opacity 0.5s';
                }, 10);

                setTimeout(() => {
                    successMessage.style.opacity = '0';
                    successMessage.style.transition = 'opacity 0.5s';
                    setTimeout(() => {
                        successMessage.style.display = 'none';
                    }, 500);
                }, 3000);
            }
        });
    </script>
</body>

</html> -->
