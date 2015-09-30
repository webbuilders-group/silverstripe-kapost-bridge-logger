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
        
        
        $('.KapostBridgeLogViewer .cms-content-tools .log-search-form').entwine({
            onsubmit: function(e) {
                // Remove empty elements and make the URL prettier
                var nonEmptyInputs,
                    url;
                
                nonEmptyInputs=this.find(':input:not(:submit)').filter(function() {
                    // Use fieldValue() from jQuery.form plugin rather than jQuery.val(),
                    // as it handles checkbox values more consistently
                    var vals = $.grep($(this).fieldValue(), function(val) { return (val);});
                    return (vals.length);
                });
                
                url=this.attr('action');
                
                if(nonEmptyInputs.length) {
                    url=$.path.addSearchParams(url, nonEmptyInputs.serialize());
                }
                
                var container=this.closest('.cms-container');
                container.loadPanel(url, "", {}, true);
                
                return false;
            }
        });
        
        
        $('.KapostBridgeLogViewer .cms-content-tools .log-search-form button[type=reset], .KapostBridgeLogViewer .cms-content-tools .log-search-form input[type=reset]').entwine({
            onclick: function(e) {
                e.preventDefault();
                
                var form=$(this).parents('form');
                
                form.clearForm();
                form.find(".dropdown select").prop('selectedIndex', 0).trigger("liszt:updated"); // Reset chosen.js
                form.submit();
            }
        });
        
        
        $('.KapostBridgeLogViewer .cms-content-fields .log-contents pre').entwine({
            ondblclick: function(e) {
                var preWidth=$(this).width();
                var preHeight=$(this).height();
                var textarea=$('<textarea class="log-contents" spellcheck="false"/>').width(preWidth).height(preHeight).val($(this).text());
                
                
                //Generate the textarea
                $(this).parent().append(textarea);
                
                
                //Hide the pre
                $(this).hide();
                
                
                //Focus the textarea
                textarea.click();
            }
        });
        
        $('.KapostBridgeLogViewer .cms-content-fields .log-contents textarea').entwine({
            onclick: function(e) {
                $(this).focus();
                $(this)[0].select();
                
                return false;
            },
            onfocusout: function(e) {
                //Re-display the pre
                $(this).siblings('pre').show();
                
                //Remove self
                $(this).remove();
            }
        });
    });
})(jQuery);