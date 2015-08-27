(function($) {
    $.entwine('ss', function($) {
        $('.KapostBridgeLogViewer .log-contents').entwine({
            redraw: function() {
                $(this).width($(this).closest('.cms-content-fields').width()-30);
            }
        });
        
        $('.KapostBridgeLogViewer .kapost-logs-list .cms-panel-content .logs li a').entwine({
            onclick: function(e) {
                var superResult=this._super(e);
                
                $('.KapostBridgeLogViewer .kapost-logs-list .cms-panel-content .logs li a.current').removeClass('current');
                
                $(this).addClass('current');
                
                return superResult;
            }
        });
        
        
        $('.KapostBridgeLogViewer .kapost-logs-list .cms-panel-content .logs-pagination .more-logs-link').entwine({
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