(function($) {
    $.entwine('ss', function($) {
        $('.KapostBridgeLogViewer .log-contents').entwine({
            redraw: function() {
                $(this).width($(this).closest('.cms-content-fields').width()-30);
            }
        });
        
        $('.KapostBridgeLogViewer .cms-content-tools .cms-panel-content .logs-pagination .more-logs-link').entwine({
            onclick: function(e) {
                var self=$(this);
                
                $.ajax({
                    url: self.attr('href'),
                    headers: {
                        'X-Pjax': 'LogEntries'
                    },
                    success: function(data) {
                        self.parent().replaceWith(data.LogEntries);
                    },
                    error: function(xhr, status, error) {
                        $('.cms-container').trigger('loadfragmenterror', {xhr: xhr, status: status, error: error});
                    }
                });
                
                return false;
            }
        });
    });
})(jQuery);