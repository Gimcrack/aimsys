<div id="content-header"><?=$content_header;?></div>
<div id="content-header-bar">
 <ul>
        	<li class="active ui-corner-top">Details</li>
            <li class="ui-corner-top">Notes</li>
            <li class="ui-corner-top">History</li>
        </ul>
</div>

<div class="content-body">
    <div id="details" class="active part">
    	<div class="data row controls">
             <?=$controls_html;?> 
    	</div>	
   		
        <div class="clear"></div>
        
        <div class="checkout_message">
        	<div class="checkout_message_msg"></div>
        </div>
        <?=$editor_html;?>
    
    </div>
    
    <div id="notes" class="part">
    	<div class="data row controls">
    		<?=$notes_controls_html;?>
    	</div>	
   
        <?=$notes_html;?>
    
    </div>
    
    <div id="history" class="part">
    	<div class="data">
    
    	</div>	
   
        History
    
    </div>
    
</div>