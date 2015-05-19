$(document).ready(function() {
    
    var moveLeft = 20;
    var moveDown = 10;
    
    $('.call').hover(function(e) {
        $('div#pop-up').show();
    }, function() {
        $('div#pop-up').hide();
    });
    
    $('.call').mousemove(function(e) {
        $("div#pop-up").css('top', e.pageY + moveDown).css('left', e.pageX + moveLeft);
    });

});