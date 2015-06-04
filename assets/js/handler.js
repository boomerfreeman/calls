$(document).ready(function() {
    
    $('#big_table').dataTable({
        "bProcessing": true,
        "bServerSide": true,
        "sAjaxSource": 'http://datatables/index.php/subscriber/datatable/',
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
