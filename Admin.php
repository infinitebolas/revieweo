<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Admin</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <?php require_once("db.php"); ?>
</head>

<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">Interface Admin</h1>

        <section class="row">


            <div class="col-md-6 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Liste Users</h2>
                    <button class="btn btn-primary btn_usr">Afficher</button>
                </div>
                <div id="user" class="mt-3"></div>
            </div>


            <div class="col-md-6 mb-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h2>Critiques</h2>
                    <button class="btn btn-primary btn_crit">Afficher</button>
                </div>
                <div id="critique" class="mt-3"></div>
            </div>

        </section>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <?php require_once("Adminjs.php"); ?>
</body>

</html>