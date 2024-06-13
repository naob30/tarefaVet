<?php
        include("conn.php");

        // Verificar se está editando um tipo de animal
        $edit = false;
        $tipoAnimal = '';
        if (isset($_GET['edit'])) {
            $edit = true;
            $codTipoAnimal = $_GET['edit'];
            $stmt = $pdo->prepare('SELECT tipoAnimal FROM tbTipoAnimal WHERE codTipoAnimal = ?');
            $stmt->execute([$codTipoAnimal]);
            $tipoAnimal = $stmt->fetch(PDO::FETCH_ASSOC)['tipoAnimal'];
        }

        // Processar formulário
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $tipoAnimal = $_POST['tipoAnimal'];
            if ($edit) {
                // Atualizar tipo de animal
                $stmt = $pdo->prepare('UPDATE tbTipoAnimal SET tipoAnimal = ? WHERE codTipoAnimal = ?');
                $stmt->execute([$tipoAnimal, $codTipoAnimal]);
            } else {
                // Inserir novo tipo de animal
                $stmt = $pdo->prepare('INSERT INTO tbTipoAnimal (tipoAnimal) VALUES (?)');
                $stmt->execute([$tipoAnimal]);
            }

            header('Location: addTipoAnimal.php?success=1');
            exit();
        }

        // Excluir tipo de animal
        if (isset($_GET['delete'])) {
            $codTipoAnimal = $_GET['delete'];
            $stmt = $pdo->prepare('DELETE FROM tbTipoAnimal WHERE codTipoAnimal = ?');
            $stmt->execute([$codTipoAnimal]);
            header('Location: addTipoAnimal.php');
            exit();
        }

        // Buscar todos os tipos de animal
        $tiposAnimais = $pdo->query('SELECT * FROM tbTipoAnimal')->fetchAll(PDO::FETCH_ASSOC);
        ?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Tipo de Animal</title>
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
        <img src="img/type.png" alt="">
        <div class="cadastro">
            <div class="card">
                <div class="card-header font-monospace"><?php echo $edit ? 'EDITAR TIPO DE ANIMAL' : 'CADASTRAR TIPO DE ANIMAL'; ?></div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])) : ?>
                        <div class="success-message" id="success-message">
                            TIPO DE ANIMAL <?php echo $edit ? 'EDITADO' : 'CADASTRADO'; ?> COM SUCESSO!
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="input-group flex-nowrap">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-tag-fill"></i></span>
                            <input type="text" class="form-control font-monospace" placeholder="Digite o tipo de animal..." aria-label="TipoAnimal" aria-describedby="addon-wrapping" name="tipoAnimal" value="<?php echo htmlspecialchars($tipoAnimal); ?>" required>
                        </div>

                        <input type="submit" value="<?php echo $edit ? 'EDITAR TIPO DE ANIMAL' : 'CADASTRAR TIPO DE ANIMAL'; ?>" class="btn btn-primary">
                    </form>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tipo de Animal</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tiposAnimais as $tipo) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tipo['codTipoAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($tipo['tipoAnimal']); ?></td>
                                <td>
                                    <a href="addTipoAnimal.php?edit=<?php echo $tipo['codTipoAnimal']; ?>" class="edit-btn"><i style="margin-right: 10px;" class="bi bi-pencil-fill"></i></a>
                                    <a href="addTipoAnimal.php?delete=<?php echo $tipo['codTipoAnimal']; ?>" class="delete-btn"><i class="bi bi-trash-fill"></i></a>
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