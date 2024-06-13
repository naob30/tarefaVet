<?php
include("conn.php");

// Buscar todos os tipos de animais e clientes para preencher os dropdowns
$tiposAnimais = $pdo->query('SELECT * FROM tbTipoAnimal')->fetchAll(PDO::FETCH_ASSOC);
$clientes = $pdo->query('SELECT * FROM tbCliente')->fetchAll(PDO::FETCH_ASSOC);

// Verificar se está editando um animal
$edit = false;
$nomeAnimal = $fotoAnimal = $codTipoAnimal = $codCliente = '';
if (isset($_GET['edit'])) {
    $edit = true;
    $codAnimal = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM tbAnimal WHERE codAnimal = ?');
    $stmt->execute([$codAnimal]);
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomeAnimal = $animal['nomeAnimal'];
    $fotoAnimal = $animal['fotoAnimal'];
    $codTipoAnimal = $animal['codTipoAnimal'];
    $codCliente = $animal['codCliente'];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeAnimal = $_POST['nomeAnimal'];
    $fotoAnimal = $_POST['fotoAnimal']; // Aqui você precisa de uma lógica para armazenar a imagem no servidor e salvar o caminho no banco de dados
    $codTipoAnimal = $_POST['codTipoAnimal'];
    $codCliente = $_POST['codCliente'];

    if ($edit) {
        // Atualizar animal
        $stmt = $pdo->prepare('UPDATE tbAnimal SET nomeAnimal = ?, fotoAnimal = ?, codTipoAnimal = ?, codCliente = ? WHERE codAnimal = ?');
        $stmt->execute([$nomeAnimal, $fotoAnimal, $codTipoAnimal, $codCliente, $codAnimal]);
    } else {
        // Inserir novo animal
        $stmt = $pdo->prepare('INSERT INTO tbAnimal (nomeAnimal, fotoAnimal, codTipoAnimal, codCliente) VALUES (?, ?, ?, ?)');
        $stmt->execute([$nomeAnimal, $fotoAnimal, $codTipoAnimal, $codCliente]);
    }

    header('Location: addAnimal.php?success=1');
    exit();
}

// Excluir animal
if (isset($_GET['delete'])) {
    $codAnimal = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tbAnimal WHERE codAnimal = ?');
    $stmt->execute([$codAnimal]);
    header('Location: addAnimal.php');
    exit();
}

// Buscar todos os animais
$animais = $pdo->query('SELECT a.*, t.tipoAnimal, c.nomeCliente FROM tbAnimal a JOIN tbTipoAnimal t ON a.codTipoAnimal = t.codTipoAnimal JOIN tbCliente c ON a.codCliente = c.codCliente')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Animal</title>
    <link rel="shortcut icon" href="img/icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .success-message {
            display: none;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            padding: 10px;
            margin-top: 10px;
            text-align: center;
            border-radius: 5px;
        }

        .table-container {
            margin-top: 20px;
        }

        table {
            background-color: #fafafa;
            color: #2a4f61;
        }

        table th,
        table td {
            padding: 10px;
            text-align: center;
        }

        .edit-btn,
        .delete-btn {
            color: #2a4f61;
        }
    </style>
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

    <div class="banner imgAnimal">
        <img class="imgAnimal" src="img/pet.png" alt="">
        <div class="cadastro">
            <div class="card">
                <div class="card-header font-monospace"><?php echo $edit ? 'EDITAR ANIMAL' : 'CADASTRAR ANIMAL'; ?></div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])) : ?>
                        <div class="success-message" id="success-message">
                            ANIMAL <?php echo $edit ? 'EDITADO' : 'CADASTRADO'; ?> COM SUCESSO!
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-paw-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Digite o nome do animal" aria-label="Nome do Animal" aria-describedby="addon-wrapping" name="nomeAnimal" value="<?php echo htmlspecialchars($nomeAnimal); ?>" required>
                        </div>
                        <!-- Aqui você precisa adicionar o campo para fazer o upload da foto do animal -->

                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-file-earmark-image-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Insira a URL da foto do animal" aria-label="Foto do Animal" aria-describedby="addon-wrapping" name="fotoAnimal" value="<?php echo htmlspecialchars($fotoAnimal); ?>">
                        </div>

                        <div class="input-group flex-nowrap mb-3">
                            <label class="input-group-text" for="tipoAnimalSelect"><i class="bi bi-list-ul"></i></label>
                            <select class="form-select font-monospace" id="tipoAnimalSelect" name="codTipoAnimal" required>
                                <option value="">Selecione o tipo de animal</option>
                                <?php foreach ($tiposAnimais as $tipoAnimal) : ?>
                                    <option value="<?php echo $tipoAnimal['codTipoAnimal']; ?>" <?php echo $tipoAnimal['codTipoAnimal'] == $codTipoAnimal ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipoAnimal['tipoAnimal']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <label class="input-group-text" for="clienteSelect"><i class="bi bi-person-fill"></i></label>
                            <select class="form-select font-monospace" id="clienteSelect" name="codCliente" required>
                                <option value="">Selecione o dono do animal</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?php echo $cliente['codCliente']; ?>" <?php echo $cliente['codCliente'] == $codCliente ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cliente['nomeCliente']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <input type="submit" value="<?php echo $edit ? 'EDITAR ANIMAL' : 'CADASTRAR ANIMAL'; ?>" class="btn btn-primary">
                    </form>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome do Animal</th>
                            <th>Foto do Animal</th>
                            <th>Tipo de Animal</th>
                            <th>Dono do Animal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($animais as $animal) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($animal['codAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($animal['nomeAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($animal['fotoAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($animal['tipoAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($animal['nomeCliente']); ?></td>
                                <td>
                                    <a href="addAnimal.php?edit=<?php echo $animal['codAnimal']; ?>" class="edit-btn"><i style="margin-right: 10px;" class="bi bi-pencil-fill"></i></a>
                                    <a href="addAnimal.php?delete=<?php echo $animal['codAnimal']; ?>" class="delete-btn"><i class="bi bi-trash-fill"></i></a>
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

// Buscar todos os clientes e tipos de animais para preencher os dropdowns
$clientes = $pdo->query('SELECT * FROM tbCliente')->fetchAll(PDO::FETCH_ASSOC);
$tiposAnimais = $pdo->query('SELECT * FROM tbTipoAnimal')->fetchAll(PDO::FETCH_ASSOC);

// Verificar se está editando um animal
$edit = false;
$nomeAnimal = $codCliente = $codTipoAnimal = '';
if (isset($_GET['edit'])) {
    $edit = true;
    $codAnimal = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM tbAnimal WHERE codAnimal = ?');
    $stmt->execute([$codAnimal]);
    $animal = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomeAnimal = $animal['nomeAnimal'];
    $codCliente = $animal['codCliente'];
    $codTipoAnimal = $animal['codTipoAnimal'];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeAnimal = $_POST['nomeAnimal'];
    $codCliente = $_POST['codCliente'];
    $codTipoAnimal = $_POST['codTipoAnimal'];

    if ($edit) {
        // Atualizar animal
        $stmt = $pdo->prepare('UPDATE tbAnimal SET nomeAnimal = ?, codCliente = ?, codTipoAnimal = ? WHERE codAnimal = ?');
        $stmt->execute([$nomeAnimal, $codCliente, $codTipoAnimal, $codAnimal]);
    } else {
        // Inserir novo animal
        $stmt = $pdo->prepare('INSERT INTO tbAnimal (nomeAnimal, codCliente, codTipoAnimal) VALUES (?, ?, ?)');
        $stmt->execute([$nomeAnimal, $codCliente, $codTipoAnimal]);
    }

    header('Location: addAnimal.php?success=1');
    exit();
}

// Excluir animal
if (isset($_GET['delete'])) {
    $codAnimal = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tbAnimal WHERE codAnimal = ?');
    $stmt->execute([$codAnimal]);
    header('Location: addAnimal.php');
    exit();
}

// Buscar todos os animais
$animais = $pdo->query('SELECT a.*, c.nomeCliente, t.tipoAnimal FROM tbAnimal a JOIN tbCliente c ON a.codCliente = c.codCliente JOIN tbTipoAnimal t ON a.codTipoAnimal = t.codTipoAnimal')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Animal</title>
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

    <div class="banner">
        <img src="img/pet.png" alt="">
        <div class="cadastro">
            <div class="card">
                <div class="card-header font-monospace"><?php echo $edit ? 'EDITAR ANIMAL' : 'CADASTRAR ANIMAL'; ?></div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])) : ?>
                        <div class="success-message" id="success-message">
                            ANIMAL <?php echo $edit ? 'EDITADO' : 'CADASTRADO'; ?> COM SUCESSO!
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-paw-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Digite o nome do animal..." aria-label="NomeAnimal" aria-describedby="addon-wrapping" name="nomeAnimal" value="<?php echo htmlspecialchars($nomeAnimal); ?>" required>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <label class="input-group-text" for="clienteSelect"><i class="bi bi-person-fill"></i></label>
                            <select class="form-select font-monospace" id="clienteSelect" name="codCliente" required>
                                <option value="">Selecione o cliente</option>
                                <?php foreach ($clientes as $cliente) : ?>
                                    <option value="<?php echo $cliente['codCliente']; ?>" <?php echo $cliente['codCliente'] == $codCliente ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($cliente['nomeCliente']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <label class="input-group-text" for="tipoAnimalSelect"><i class="bi bi-tag-fill"></i></label>
                            <select class="form-select font-monospace" id="tipoAnimalSelect" name="codTipoAnimal" required>
                                <option value="">Selecione o tipo de animal</option>
                                <?php foreach ($tiposAnimais as $tipo) : ?>
                                    <option value="<?php echo $tipo['codTipoAnimal']; ?>" <?php echo $tipo['codTipoAnimal'] == $codTipoAnimal ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($tipo['tipoAnimal']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <input type="submit" value="<?php echo $edit ? 'EDITAR ANIMAL' : 'CADASTRAR ANIMAL'; ?>" class="btn btn-primary">
                    </form>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Cliente</th>
                            <th>Tipo de Animal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($animais as $animal) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($animal['codAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($animal['nomeAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($animal['nomeCliente']); ?></td>
                                <td><?php echo htmlspecialchars($animal['tipoAnimal']); ?></td>
                                <td>
                                    <a href="addAnimal.php?edit=<?php echo $animal['codAnimal']; ?>" class="edit-btn"><i style="margin-right: 10px;" class="bi bi-pencil-fill"></i></a>
                                    <a href="addAnimal.php?delete=<?php echo $animal['codAnimal']; ?>" class="delete-btn"><i class="bi bi-trash-fill"></i></a>
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
