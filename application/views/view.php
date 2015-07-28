<!DOCTYPE html>
<html lang="EN">
    <head>
        <title><?=$title?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css">
        <link rel="stylesheet" href="/assets/css/handler.css">
        <script src="//code.jquery.com/jquery-1.11.1.min.js"></script>
        <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
        <script src="//cdn.datatables.net/1.10.7/js/jquery.dataTables.min.js"></script>
        <script src="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.js"></script>
        <script>
            $(document).ready(function() {
                
                // Delete #log datatable to make this working:                
                $("#modal").dialog({ autoOpen: false });
                $("tr#call").click(function() {
                    $("#modal").dialog("open");
                });
                
                $('#log').dataTable({
                    "processing": true,
                    "serverSide": true,
                    "paging": true,
                    "ajax": {
                        "url": "<?=base_url('index.php/serverside/')?>",
                        "type": "GET"
                    }
                });
            });
        </script>
    </head>
    <body class="container-fluid">
        <div id="langbar">
            <a href="<?=$_SERVER['SCRIPT_NAME'] . '/log/ru/'?>" class="btn btn-primary">RU</a>
            <a href="<?=$_SERVER['SCRIPT_NAME'] . '/log/en/'?>" class="btn btn-primary">EN</a>
        </div>
        <h1 id="heading"><?=$heading . date("H:i, j.m.Y")?></h1>
        <?=$table?>
        <div id="pop-up">
            <h3>Pop-up div Successfully Displayed</h3>
            <p>This div only appears when the trigger link is hovered over. Otherwise it is hidden from view.</p>
        </div>
<!--        <div id="modal" title="Modal window for call #">
            <?=$table?>
        </div>-->
    </body>
</html>
