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
                var panel=$(this).closest('.cms-content-tools');
                
                panel.addClass('loading');
                
                $.ajax({
                    url: self.attr('href'),
                    headers: {
                        'X-Pjax': 'LogEntries'
                    },
                    success: function(data) {
                        self.parent().replaceWith(data.LogEntries);
                        
                        panel.removeClass('loading');
                    },
                    error: function(xhr, status, error) {
                        $('.cms-container').trigger('loadfragmenterror', {xhr: xhr, status: status, error: error});
                        
                        panel.removeClass('loading');
                    }
                });
                
                return false;
            }
        });
    });
})(jQuery);