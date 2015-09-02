
<div id="top">
	<div id="top-logo"></div>
	<div id="top-text">Aircraft Inventory & Maintenance System</div>
    <div id="tab-tools" class="ui-corner-top">
    	<a href="#" id="lb-savetabs" class="easyui-linkbutton" iconcls="icon-save" tt="Save Tab Layout"></a>
        <a href="#" id="lb-scrolltableft" class="easyui-linkbutton" iconcls="icon-prev" tt="Scroll Tabs Left"></a>
        <a href="#" id="lb-scrolltabright" class="easyui-linkbutton" iconcls="icon-next" tt="Scroll Tabs Right"></a>
        <div class="toolbar-separator"></div>
        <a id="manage_account" tt="Manage Account" class="easyui-linkbutton" iconcls="icon-user" href="index.php?controller=main&option=users&view=account"><?=$user['use_username'];?></a>
        <a tt="logout" class="easyui-linkbutton" iconcls="icon-remove" href="index.php?action=logout"><span id="countdown"></span></a>
    </div>
	<div id="tab-bar">
		<ul>
        	
		</ul>
	</div>
</div>
<div id="left">
	<div id="arrow" tt="Toggle Navigation Pane"></div>
	<?php include("menu.html.php"); ?>
    <div id="heightdiv"></div>
</div>
<div id="main" class="ui-corner-bottom shadow">
	
</div>

<div id="div_change_password" style="display:none">
    <form id="frm_change_password" name="frm_change_password" onsubmit="return(false)"  >
        <div class="div-editor-col"></div>
        <div class="div-editor-col">
            <div class="div-editor-cell">
                <label for="password_1">Enter Password : </label>&nbsp;
            </div>
            <div class="div-editor-cell">
                <label for="password_2">Confirm Password : </label>&nbsp;
            </div>
        </div>
        <div class="div-editor-col">
            <div class="div-editor-cell">
                <input type="password" class="editor-element required-input ui-state-default ui-corner-all" validType="min>=6" id="password_1" name="password_1" size="20" value="" autocomplete="off"  />&nbsp;
            </div>
            <div class="div-editor-cell">
                <input type="password" class="editor-element required-input ui-state-default ui-corner-all" validType="field==password_1" id="password_2" name="password_2" size="20" value="" autocomplete="off" />&nbsp;
            </div>
        </div>
        <div class="div-editor-col"></div>
    
    </form>
</div>

<script src="index.php?controller=tab&option=checksession"></script>