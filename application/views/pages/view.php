    <body class="container-fluid">
        <a href="<?=$_SERVER['SCRIPT_NAME'] . '/log/ru/'?>">RU</a>
        <a href="<?=$_SERVER['SCRIPT_NAME'] . '/log/en/'?>">EN</a>
        <input type="text" class="form-control" placeholder="Wildcard">
        <table class="table table-striped table-hover">
            <tr>
                <td><a href="http://calls/log/sort/caller/"><h3><strong>Caller</strong></h3></a></td>
                <td><h3><strong>Event</strong></h3></td>
                <td><a href="http://calls/log/sort/reciever/"><h3><strong>Reciever</strong></h3></a></td>
                <td><h3><strong>Timestamp</strong></h3></td>
            </tr>
            <?php foreach ($genData as $genVar): ?>
            <tr class="call">
            <?php foreach ($dbData as $dbVar): ?>
                <td><h4><?=$genVar->$dbVar?></h4></td>
            <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </table>
        <div id="pop-up">
            <table class="table table-striped table-hover">
                <tr>
                    <td>Timestamp</td>
                    <td>Talk Duration</td>
                    <td>Reciever</td>
                    <td>Type</td>
                </tr>
            </table>
            <h3>Pop-up div Successfully Displayed</h3>
            <h6>Data</h6>
        </div>
    </body>
