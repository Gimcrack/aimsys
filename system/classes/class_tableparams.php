<?php
include_once("class_db.php");

class tableparamlist {
	
	public $name				;//	Name of the table
	public $description			;// Description of the table
	public $joins				;// List of joins to use when querying the table
	public $relations			;// Table relations	
}

// Setup the default col parameter parameters
class tableparamdefaults {
	
	public $name 				= array("type" => "text","label" => "Table Name","helptext" => "text - What is the name of the table?","defaultvalue" => ""); 	//	Is column editable
	public $description			= array("type" => "text","label" => "Description","helptext" => "text - Table description","defaultvalue" => "");				//	Is column visible
	public $joins				= array("type" => "text","label" => "Table Joins","helptext" => "text - List any table joins.","defaultvalue" => "");			//	Is column quickview enabled
	public $relations			= array("type" => "text","label" => "Table Relations","helptext" => "text - List any table relations.","defaultvalue" => "");	//	If quickview is enabled, is it on by default

}





?>