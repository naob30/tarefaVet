<?php
include("conn.php");

// Verificar se está editando um veterinário
$edit = false;
$nomeVet = '';
if (isset($_GET['edit'])) {
    $edit = true;
    $codVet = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT nomeVet FROM tbVeterinario WHERE codVet = ?');
    $stmt->execute([$codVet]);
    $veterinario = $stmt->fetch(PDO::FETCH_ASSOC);
    $nomeVet = $veterinario['nomeVet'];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nomeVet = $_POST['nomeVet'];
    if ($edit) {
        // Atualizar veterinário
        $stmt = $pdo->prepare('UPDATE tbVeterinario SET nomeVet = ? WHERE codVet = ?');
        $stmt->execute([$nomeVet, $codVet]);
    } else {
        // Inserir novo veterinário
        $stmt = $pdo->prepare('INSERT INTO tbVeterinario (nomeVet) VALUES (?)');
        $stmt->execute([$nomeVet]);
    }

    header('Location: addVet.php?success=1');
    exit();
}

// Excluir veterinário
if (isset($_GET['delete'])) {
    $codVet = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tbVeterinario WHERE codVet = ?');
    $stmt->execute([$codVet]);
    header('Location: addVet.php');
    exit();
}

// Buscar todos os veterinários
$veterinarios = $pdo->query('SELECT * FROM tbVeterinario')->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Veterinário</title>
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
        <img src="img/vet.png" alt="">
        <div class="cadastro">
            <div class="card">
                <div class="card-header font-monospace"><?php echo $edit ? 'EDITAR VETERINÁRIO' : 'CADASTRAR VETERINÁRIO'; ?></div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])) : ?>
                        <div class="success-message" id="success-message">
                            VETERINÁRIO <?php echo $edit ? 'EDITADO' : 'CADASTRADO'; ?> COM SUCESSO!
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-person-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Digite o nome do veterinário..." aria-label="Username" aria-describedby="addon-wrapping" name="nomeVet" value="<?php echo htmlspecialchars($nomeVet); ?>" required>
                        </div>

                        <input type="submit" value="<?php echo $edit ? 'EDITAR VETERINÁRIO' : 'CADASTRAR VETERINÁRIO'; ?>" class="btn btn-primary">
                    </form>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($veterinarios as $vet) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($vet['codVet']); ?></td>
                                <td><?php echo htmlspecialchars($vet['nomeVet']); ?></td>
                                <td>
                                    <a href="addVet.php?edit=<?php echo $vet['codVet']; ?>" class="edit-btn"><i style="margin-right: 10px;" class="bi bi-pencil-fill"></i></a>
                                    <a href="addVet.php?delete=<?php echo $vet['codVet']; ?>" class="delete-btn"><i class="bi bi-trash-fill"></i></a>
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














































































