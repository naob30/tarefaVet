<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Clínica Veterinária</title>
    <link rel="shortcut icon" href="img/icon.png" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="styleindex.css">
</head>

<body>
    <header>
        <div class="logo">
            <img src="img/icon.png" alt="">
            <h3 class="text-uppercase">Clínica Veterinária</h3>
        </div>
        <div class="atendimento">
            <a href="addAtendimento.php"><i style="margin-right: 10px;"class="bi bi-calendar-check"></i>MARQUE AGORA O ATENDIMENTO</a>
        </div>
    </header>

    <div class="banner">
        <img src="img/banner.png" alt="">

        <div class="opcoes">

            <div class="row">
                <div class="col-sm-6 mb-3 mb-sm-0">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">VETERINÁRIO</h5>
                            <p class="card-text">Cadastre aqui o veterinário.</p>
                            <a href="addVet.php" class="btn btn-primary">CADASTRAR</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">CLIENTE</h5>
                            <p class="card-text">Cadastre aqui o cliente.</p>
                            <a href="addCliente.php" class="btn btn-primary">CADASTRAR</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-6 mb-sm-0 opacity">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">TIPO DO PET</h5>
                            <p class="card-text">Cadastre aqui o tipo do PET.</p>
                            <a href="addTipoAnimal.php" class="btn btn-primary">CADASTRAR</a>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">PET</h5>
                            <p class="card-text">Cadastre aqui o PET.</p>
                            <a href="addAnimal.php" class="btn btn-primary">CADASTRAR</a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>

    <footer>
        <h5 class="font-monospace" style="font-size: 15px; color: #008186;">&copy; 2023 Nayara Brabo. All rights reserved.</h5>
    </footer>

</body>

</html>