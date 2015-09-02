<!--

	#confirm_autocheckin, displays after 30 seconds when a record has been opened for editing.

-->
<div id="confirm_autocheckin" style="display:none" title="Continue Editing?">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>You have a record open for editing. <br>
<br>
If you wish to continue editing, click 'Continue Editing'. Otherwise click Save to save any changes and close the record, or click Cancel to cancel any changes and close the record.<br>
<br>
Note: any unsaved changes will be lost.</p>

</div>


<!--

	#form_container_0 - Add new user form

-->
<div id="form_container_0" style="display:none;" title="Add New User">
<form onsubmit="return(false)" enctype="multipart/form-data" autocomplete="off" action="manage_users.php" id="frm_insert" name="frm_insert" class="form_table">
<fieldset>

<div class="left_col"><label for="firstname" class="required-label">First Name : </label></div>
<div class="right_col"><input type="text" validtype="Anything" value="" class="required-input ui-state-default ui-corner-all" id="firstname" name="use_firstname"></div>

<div class="clear"></div>

<div class="left_col"><label for="lastname" class="required-label">Last Name : </label></div>
<div class="right_col"><input type="text" validtype="Anything" value="" class="required-input ui-state-default ui-corner-all" id="lastname" name="use_lastname"></div>

<div class="clear"></div>

<div class="left_col"><label for="username" class="required-label">Username : </label></div>
<div class="right_col"><input type="text" validtype="Anything" value="" class="required-input ui-state-default ui-corner-all" id="username" name="use_username"></div>

<div class="clear"></div>

<div class="left_col"><label for="password" class="required-label">Password : </label></div>
<div class="right_col"><input type="password" validtype="min&gt;6" value="" class="required-input ui-state-default ui-corner-all" id="password" name="use_password"></div>

<div class="clear"></div>

<div class="left_col"><label for="password2" class="required-label">Confirm Password : </label></div>
<div class="right_col"><input type="password" validtype="field==use_password" value="" class="required-input ui-state-default ui-corner-all" id="password2" name="password2"></div>

<div class="clear"></div>

<div class="left_col"><label for="email" class="required-label">Email : </label></div>
<div class="right_col"><input type="text" validtype="Email Address" value="" class="required-input ui-state-default ui-corner-all" id="email" name="use_email"></div>

<div class="clear"></div>

<div class="left_col"><label for="access" class="required-label">User Group : </label></div>
<div class="right_col"><select multiple="true" title="User Group : " class="jquery-singleselect" id="access" name="use_access[]" style="display: none;">
<option value="0">Mx Control</option>
<option value="1">Staff</option>
<option value="2">Pilots</option>
<option value="3">Maintenance</option>
<option value="4">Parts</option>
<option value="5">Ticketing Agents</option>
<option value="6">Business Admin</option>
</select></div>

<input type="hidden" readonly="true" validtype="" value="" class="optional-input" id="frm_name" name="frm_name">

<input type="hidden" readonly="true" validtype="" value="aimusers" class="optional-input" id="table_name" name="table_name">
</fieldset>
</form>

</div>

<!--

	#password_reset - Prompt to confirm resetting user's password
    
-->

<div id="password_reset" style="display:none" title="Reset User's Password">
<p><span class="ui-icon ui-icon-alert" style="float:left; margin:0 7px 20px 0;"></span>Reset this user's password? <br>
</div>


<!--

	#delete_record_0 - Delete user form

-->
<div id="delete_record_0" style="display:none;" title="Delete User - Type a comment to continue">
<form onsubmit="return(false)" enctype="multipart/form-data" autocomplete="off" action="manage_users.php" id="frm_delete" name="frm_delete" class="form_table">
<fieldset>

<div class="left_col"><label for="comments" class="required-label">Comments : </label></div>
<div class="right_col"><textarea rows="5" style="width:90%" validtype="Anything" class="required-input ui-state-default ui-corner-all" id="comments" name="comments"></textarea></div>

<div class="clear"></div>

<input type="hidden" value="" class="optional-input" id="cid" name="use_id">
<input type="hidden" value="frm_delete" class="optional-input" id="frm_name" name="frm_name">
<input type="hidden" value="aimusers" class="optional-input" id="table_name" name="table_name">
</fieldset>
</form>

</div>