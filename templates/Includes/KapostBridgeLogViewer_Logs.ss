<div class="cms-content-tools west cms-panel cms-panel-layout cms-content-view kapost-logs-list" id="kapostbridgelogviewer-logs" data-expandOnClick="true" data-layout-type="border">
	<div class="cms-panel-content center">
        <h3 class="cms-panel-header"><%t KapostBridgeLogViewer.LOGS "_Logs" %> <a href="$Link" class="ss-ui-button refresh-logs" data-icon="arrow-circle-double"><%t KapostBridgeLogViewer.REFRESH "_Refresh" %></a></h3>
        
        <% if $Logs && $Logs.MoreThanOnePage && $Logs.NotFirstPage %>
            <p class="logs-pagination"><a href="$Logs.PreviousLink" class="more-logs-link"><%t KapostBridgeLogViewer.PREVIOUS_ENTRIES "_Previous Entries" %></a></p>
        <% end_if %>
        
        <% include KapostBridgeLogViewer_LogsList %>
	</div>
    
    <div class="cms-panel-content-collapsed">
        <h3 class="cms-panel-header"><%t KapostBridgeLogViewer.LOGS "_Logs" %></h3>
    </div>
</div>