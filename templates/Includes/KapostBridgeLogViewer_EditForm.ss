<form $FormAttributes data-layout-type="border">
    <div class="cms-content-fields center cms-panel-padded" data-layout-type="border">
        <% if $Message %>
            <p id="{$FormName}_error" class="message $MessageType">$Message</p>
        <% else %>
            <p id="{$FormName}_error" class="message $MessageType" style="display: none"></p>
        <% end_if %>
        
        <fieldset>
            <% if $Legend %><legend>$Legend</legend><% end_if %>
            <% loop $Fields %>
                $FieldHolder
            <% end_loop %>
            <div class="clear"><!-- --></div>
        </fieldset>
    </div>
    
    <% if $Actions %>
        <div class="cms-content-actions cms-content-controls south">
            <div class="Actions">
                <% loop $Actions %>
                    $Field
                <% end_loop %>
            </div>
        </div>
    <% end_if %>
</form>