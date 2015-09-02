<?php 
/*********************************************************************************************************************************
||																																||
||										AIMSys - Aircraft Inventory & Maintenance System										||
||										Jeremy Bloomstrom																		||
||										Ingenious Design																		||
||										jeremy@in.genio.us																		||
||										March 27, 2013																			||
||																																||
|________________________________________________________________________________________________________________________________|
||																																||
||																																||
||										editor.php																				||
||										VIEW																					||
||																																||
*********************************************************************************************************************************/

// Do Some Logic stuff
$table 		= req::_('table');
$rowid 		= req::_('rowid','new');
$editing	= req::_('editing',false);

$editing = ($editing == "false") ? false : $editing;

// Determine if new record or update
$content_header = ($rowid <> 'new') ? 'View' : 'New';

// Initialize table by getting params and populating with data.
$oEditor = new editor($table,$rowid);
$oEditor->_getData();
$editor_html = $oEditor->_html();

// Controls HTML
$edit_display = ($rowid <> 'new' && empty($editing) ) ? '' : 'display:none';
$new_display  = ($rowid <> 'new' && empty($editing) ) ? 'display:none' : '';
$controls_html = <<<HTML
<a href="#" id="lb-edit" style="{$edit_display}" class="easyui-linkbutton" iconcls="icon-edit" tt="Edit Record">Edit</a>
<a href="#" id="lb-refresh" style="{$edit_display}" class="easyui-linkbutton" iconcls="icon-reload" tt="Refresh Record">Refresh</a>
<a href="#" id="lb-save" style="{$new_display}" class="easyui-linkbutton" iconcls="icon-save" tt="Save Changes">Save</a>
<a href="#" id="lb-reset" style="{$new_display}" class="easyui-linkbutton" iconcls="icon-reload" tt="Reset Changes">Reset</a>
<div class="toolbar-separator"></div>
<a href="#" id="lb-close" style="{$edit_display}" class="easyui-linkbutton" iconcls="icon-cancel" tt="Close Record">Close</a>
<a href="#" id="lb-cancel" style="{$new_display}" class="easyui-linkbutton" iconcls="icon-cancel" tt="Cancel Changes">Cancel</a>
<div class="toolbar-separator"></div> 
HTML;

// Get notes

$oNotes = new Notes($table,$rowid);
$oNotes->_getNotes();

$oNoteTable = new table("aimnotes",$oNotes->notes);
$notes_html = $oNoteTable->_quickview();

$notes_controls_html = <<<HTML
<a href="#" id="lb-new-note" class="easyui-linkbutton" iconcls="icon-edit" tt="New Note" onclick="new_note('{$table}','{$rowid}')">New</a>
<div class="toolbar-separator"></div>
<a href="#" id="lb-refresh-note" class="easyui-linkbutton" iconcls="icon-reload" tt="Refresh Notes">Refresh</a>
HTML;

// Get View
$oView = new view($view);


// Display View Script and View HTML
echo document::_addScript( $oView->_script() );
echo document::_addScript(  "ready.js" );
include_once( $oView->_html() );

echo '<div class="clear"></div>';
//pre($data);

?>