<div class="cms-content-tools west cms-panel cms-panel-layout" data-expandOnClick="true" data-layout-type="border">
	<div class="cms-panel-content center">
        <h3 class="cms-panel-header"><%t KapostBridgeLogViewer.LOGS "_Logs" %></h3>
        
        <% if $Logs %>
            <ul class="logs">
                <% loop $Logs %>
                    <li><a href="$Top.Link('view')/$ID" class="cms-panel-link<% if $Top.currentPageID==$ID %> current<% end_if %>" data-pjaxTarget="CurrentForm">$Method.XML<br /><span>$Created.FormatFromSettings</span></a></li>
                <% end_loop %>
            </ul>
        <% end_if %>
	</div>
    
    <div class="cms-panel-content-collapsed">
        <h3 class="cms-panel-header"><%t KapostBridgeLogViewer.LOGS "_Logs" %></h3>
    </div>
</div>