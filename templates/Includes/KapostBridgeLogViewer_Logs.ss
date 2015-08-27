<div class="cms-content-tools west cms-panel cms-panel-layout" data-expandOnClick="true" data-layout-type="border">
	<div class="cms-panel-content center">
        <h3 class="cms-panel-header"><%t KapostBridgeLogViewer.LOGS "_Logs" %></h3>
        
        
        <% if $Logs && $Logs.MoreThanOnePage && $Logs.NotFirstPage %>
            <p class="logs-pagination"><a href="$Logs.PreviousLink" class="more-logs-link"><%t KapostBridgeLogViewer.PREVIOUS_ENTRIES "_Previous Entries" %></a></p>
        <% end_if %>
        
        <% include KapostBridgeLogViewer_LogsList %>
	</div>
    
    <div class="cms-panel-content-collapsed">
        <h3 class="cms-panel-header"><%t KapostBridgeLogViewer.LOGS "_Logs" %></h3>
    </div>
</div>