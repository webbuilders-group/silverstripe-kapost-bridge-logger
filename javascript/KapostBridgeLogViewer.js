(function($) {
    $.entwine('ss', function($) {
        $('.KapostBridgeLogViewer .log-contents').entwine({
            redraw: function() {
                $(this).width($(this).closest('.cms-content-fields').width()-30);
            }
        });
    });
})(jQuery);