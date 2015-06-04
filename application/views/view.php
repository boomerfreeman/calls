<!DOCTYPE html>
<html lang="EN">
    <head>
        <title><?=$title?></title>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css">
        <link rel="stylesheet" href="//cdn.datatables.net/1.10.7/css/jquery.dataTables.css">
        <link rel="stylesheet" href="/htdocs/css/style.css">
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.4/js/bootstrap.min.js"></script>
        <script src="<?=base_url('assets/js/jquery.dataTables.min.js')?>"></script>
        <script>
            $(document).ready(function() {
    
                $('#big_table').dataTable({
                    "bProcessing": true,
                    "bServerSide": true,
                    "sAjaxSource": '<?=base_url('index.php/log/datatable/')?>',
                    "bJQueryUI": true,
                    "sPaginationType": "full_numbers",
                    "iDisplayStart ":20,

                    'fnServerData': function(sSource, aoData, fnCallback)
                    {
                        $.ajax ({
                            'dataType': 'json',
                            'type'    : 'POST',
                            'url'     : sSource,
                            'data'    : aoData,
                            'success' : fnCallback
                        });
                    }
                });

                $('.call').hover(function(e) {
                    $('div#pop-up').show();
                }, function() {
                    $('div#pop-up').hide();
                });

                $('.call').mousemove(function(e) {
                    $("div#pop-up").css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
                });
            });
        </script>
    </head>
    <body class="container-fluid">
        <a href="<?=$_SERVER['SCRIPT_NAME'] . '/log/ru/'?>">RU</a>
        <a href="<?=$_SERVER['SCRIPT_NAME'] . '/log/en/'?>">EN</a>
        <h1>Logs for <?=date("F j, Y, g:i a")?></h1>
            <?=$table?>
    </body>
</html>
