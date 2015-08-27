<% if $Logs %>
    <ul class="logs">
        <% loop $Logs %>
            <li><a href="$Top.Link('view')/$ID" class="cms-panel-link" data-pjax-target="CurrentForm,Breadcrumbs">$Method.XML<br /><span>$Created.FormatFromSettings</span></a></li>
        <% end_loop %>
    </ul>
    
    <% if $Logs.MoreThanOnePage && $Logs.NotLastPage %>
        <p class="logs-pagination"><a href="$Logs.NextLink" class="more-logs-link"><%t KapostBridgeLogViewer.MORE_ENTRIES "_More Entries" %></a></p>
    <% end_if %>
<% end_if %>