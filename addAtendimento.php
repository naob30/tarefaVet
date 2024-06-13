<?php
include("conn.php");

// Buscar todos os animais e veterinários para preencher os dropdowns
$animais = $pdo->query('SELECT * FROM tbAnimal')->fetchAll(PDO::FETCH_ASSOC);
$veterinarios = $pdo->query('SELECT * FROM tbVeterinario')->fetchAll(PDO::FETCH_ASSOC);

// Verificar se está editando um atendimento
$edit = false;
$codAnimal = $codVet = $dataAtendimento = $horaAtendimento = '';
if (isset($_GET['edit'])) {
    $edit = true;
    $codAtendimento = $_GET['edit'];
    $stmt = $pdo->prepare('SELECT * FROM tbAtendimento WHERE codAtendimento = ?');
    $stmt->execute([$codAtendimento]);
    $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);
    $codAnimal = $atendimento['codAnimal'];
    $codVet = $atendimento['codVet'];
    $dataAtendimento = $atendimento['DataAtendimento'];
    $horaAtendimento = $atendimento['HoraAtendimento'];
}

// Processar formulário
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $codAnimal = $_POST['codAnimal'];
    $codVet = $_POST['codVet'];
    $dataAtendimento = $_POST['dataAtendimento'];
    $horaAtendimento = $_POST['horaAtendimento'];

    if ($edit) {
        // Atualizar atendimento
        $stmt = $pdo->prepare('UPDATE tbAtendimento SET codAnimal = ?, codVet = ?, DataAtendimento = ?, HoraAtendimento = ? WHERE codAtendimento = ?');
        $stmt->execute([$codAnimal, $codVet, $dataAtendimento, $horaAtendimento, $codAtendimento]);
    } else {
        // Inserir novo atendimento
        $stmt = $pdo->prepare('INSERT INTO tbAtendimento (codAnimal, codVet, DataAtendimento, HoraAtendimento) VALUES (?, ?, ?, ?)');
        $stmt->execute([$codAnimal, $codVet, $dataAtendimento, $horaAtendimento]);
    }

    header('Location: addAtendimento.php?success=1');
    exit();
}

// Excluir atendimento
if (isset($_GET['delete'])) {
    $codAtendimento = $_GET['delete'];
    $stmt = $pdo->prepare('DELETE FROM tbAtendimento WHERE codAtendimento = ?');
    $stmt->execute([$codAtendimento]);
    header('Location: addAtendimento.php');
    exit();
}

// Buscar todos os atendimentos   (OS JOIN ESTÃO AQUI)
$atendimentos = $pdo->query('SELECT 
                                a.*, an.nomeAnimal, v.nomeVet 
                            FROM tbAtendimento a 
                            JOIN tbAnimal an ON a.codAnimal = an.codAnimal 
                            JOIN tbVeterinario v ON a.codVet = v.codVet')->fetchAll(PDO::FETCH_ASSOC);

// Formatar datas dos atendimentos
foreach ($atendimentos as &$atendimento) {
    $date = new DateTime($atendimento['DataAtendimento']);
    $atendimento['DataAtendimento'] = $date->format('d/m/Y');
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Atendimento</title>
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

    <div class="banner imgAtendimento">
        <img class='imgAtendimento' src="img/calendar2.png" alt="">
        <div class="cadastro">
            <div class="card">
                <div class="card-header font-monospace"><?php echo $edit ? 'EDITAR ATENDIMENTO' : 'CADASTRAR ATENDIMENTO'; ?></div>
                <div class="card-body">
                    <?php if (isset($_GET['success'])) : ?>
                        <div class="success-message" id="success-message">
                            ATENDIMENTO <?php echo $edit ? 'EDITADO' : 'CADASTRADO'; ?> COM SUCESSO!
                        </div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="input-group flex-nowrap mb-3">
                            <label class="input-group-text" for="animalSelect"><i class="bi bi-paw-fill"></i></label>
                            <select class="form-select font-monospace" id="animalSelect" name="codAnimal" required>
                                <option value="">Selecione o animal</option>
                                <?php foreach ($animais as $animal) : ?>
                                    <option value="<?php echo $animal['codAnimal']; ?>" <?php echo $animal['codAnimal'] == $codAnimal ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($animal['nomeAnimal']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <label class="input-group-text" for="vetSelect"><i class="bi bi-person-fill"></i></label>
                            <select class="form-select font-monospace" id="vetSelect" name="codVet" required>
                                <option value="">Selecione o veterinário</option>
                                <?php foreach ($veterinarios as $vet) : ?>
                                    <option value="<?php echo $vet['codVet']; ?>" <?php echo $vet['codVet'] == $codVet ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($vet['nomeVet']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="input-group flex-nowrap mb-3">
                            <span class="input-group-text" id="addon-wrapping"><i class="bi bi-calendar-fill"></i></span>
                            <input type="date" class="form-control font-monospace" placeholder="Data do atendimento" aria-label="DataAtendimento" aria-describedby="addon-wrapping" name="dataAtendimento" value="<?php echo htmlspecialchars($dataAtendimento); ?>" required>
                            <input type="time" class="form-control font-monospace" placeholder="Hora do atendimento" aria-label="HoraAtendimento" aria-describedby="addon-wrapping" name="horaAtendimento" value="<?php echo htmlspecialchars($horaAtendimento); ?>" required>
                        </div>

                        <input type="submit" value="<?php echo $edit ? 'EDITAR ATENDIMENTO' : 'CADASTRAR ATENDIMENTO'; ?>" class="btn btn-primary">
                    </form>
                </div>
            </div>

            <div class="table-container">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Animal</th>
                            <th>Veterinário</th>
                            <th>Data do Atendimento</th>
                            <th>Hora do Atendimento</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($atendimentos as $atendimento) : ?>
                            <tr>
                                <td><?php echo htmlspecialchars($atendimento['codAtendimento']); ?></td>
                                <td><?php echo htmlspecialchars($atendimento['nomeAnimal']); ?></td>
                                <td><?php echo htmlspecialchars($atendimento['nomeVet']); ?></td>
                                <td><?php echo htmlspecialchars($atendimento['DataAtendimento']); ?></td>
                                <td><?php echo htmlspecialchars($atendimento['HoraAtendimento']); ?></td>
                                <td>
                                    <a href="addAtendimento.php?edit=<?php echo $atendimento['codAtendimento']; ?>" class="edit-btn"><i style="margin-right: 10px;" class="bi bi-pencil-fill"></i></a>
                                    <a href="addAtendimento.php?delete=<?php echo $atendimento['codAtendimento']; ?>" class="delete-btn"><i class="bi bi-trash-fill"></i></a>
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
