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
                
                // Main log table:
                $("#log").dataTable({
                    "processing":   true,
                    "serverSide":   true,
                    "paging":       true,
                    "ajax":         "<?=base_url("serverside/mainLog/")?>" + "/"
                });
                
                // Hide modal windows at start:
                $("#modal").dialog({autoOpen: false});
                $("#extend").dialog({autoOpen: false});
                
                // If table row is clicked:
                $("#log>tbody").on("click", "tr", function () {
                    
                    // Get caller and reciever numbers:
                    var log_caller = $(this).find("td").eq(0).html();
                    var log_reciever = $(this).find("td").eq(2).html();
                    
                    if (log_reciever.length === 0) {
                        log_reciever = 'null';
                    }
                    
                    // Fill windows with data via AJAX:
                    $("#modal").dataTable({
                        "sort":     false,
                        "filter":   false,
                        "destroy":  true,
                        "paging":   false,
                        "ajax":     "<?=base_url('serverside/modalLog')?>" + "/" + log_caller + "/" + log_reciever + "/"
                    });
                    
                    $("#extend").dataTable({
                        "sort":     false,
                        "filter":   false,
                        "destroy":  true,
                        "paging":   false,
                        "ajax":     "<?=base_url('serverside/extendLog')?>" + "/" + log_caller + "/"
                    });
                    
                    // Set title for a call:
                    $.ajax({
                        url: "<?=base_url('serverside/modalLog')?>" + "/" + log_caller + "/" + log_reciever + "/",
                        type: "GET",
                        dataType: "JSON",
                        async: false,
                        success: function (rows) {
                            
                            // Create JSON string and count its length:
                            JSON.stringify(rows);
                            var count = rows.data.length;
                            
                            switch (count) {
                                case 1: call_title = 'Cancelled call'; break;
                                case 4: call_title = 'Non-dialled call'; break;
                                case 5: call_title = 'Regular call'; break;
                                default: call_title = 'Cancelled call';
                            }
                        }
                    });
                    
                    // Set modal windows options:
                    $("#modal").dialog("option", {
                        "minHeight": 400,
                        "minWidth": 700,
                        "maxHeight": 600,
                        "maxWidth": 750,
                        "position": {
                            my: "top-25",
                            at: "left",
                            of: "#heading"
                        },
                        "title": "Number " + log_caller + " : " + call_title
                    });
                    
                    $("#extend").dialog("option", {
                        "minHeight": 300,
                        "minWidth": 650,
                        "maxHeight": 400,
                        "maxWidth": 700,
                        "position": {
                            my: "top-25",
                            at: "right",
                            of: "#heading"
                        },
                        "title": "All calls by number " + log_caller
                    });
                    
                    // Open modal windows:
                    $("#modal").dialog("open");
                    $("#extend").dialog("open");
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
        <div title="Main log table"><?=$table?></div>
        <div><?=$modal?></div>
        <div><?=$extend?></div>
    </body>
</html>
