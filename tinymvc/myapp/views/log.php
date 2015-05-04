<?php

$dbVars = array ('CALLER', 'RECORD_EVENT_ID', 'RECIEVER', 'RECORD_DATE');

?>
<!DOCTYPE html>
<html lang="EN">
    <head>
        <title><?=$title?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="css/style.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
    </head>
    <body class="container-fluid">
        <table class="table table-striped table-hover">
            <tr>
                <td>
                    <a href="<?=$_SERVER['SCRIPT_NAME'] . '/main/sort'?>"><h4><strong><?=$caller?></strong></h4></a>
                </td>
                <td>
                    <h4><strong><?=$event?></strong></h4>
                </td>
                <td>
                    <a href="<?=$_SERVER['SCRIPT_NAME'] . '/main/rsort'?>"><h4><strong><?=$reciever?></strong></h4></a>
                </td>
                <td>
                    <h4><strong><?=$time?></strong></h4>
                </td>
            </tr>
            <?php for ($i=0; $i < count($data); $i++): ?>
            <tr>
                <?php foreach ($dbVars as $var): ?>
                <td>
                    <h4><?=$data[$i][$var]?></h4>
                </td>
                <?php endforeach; ?>
            </tr>
            <?php endfor; ?>
        </table>
    </body>
</html>