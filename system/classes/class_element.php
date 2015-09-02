<?php 

function testBed(){
	$p = new el('p');
	$br = new el('br');
	echo 'This sentence should break here'.$br.'if break element was created.'.$br.$br;
	
	$img = new el('img',$atts=array('src'=>'../../images/test1.jpg','width'=>'250','height'=>'149'));
	echo 'A plain image should appear here: '.$img.$br.$br;
	
	$atts=array("href"=>"http://www.dogpile.com/",'title'=>'Can "you" guess?','text'=>'My favourite search engine. =)');
	$a = new el('a',$atts);
	echo $a.$br.$br;
	
	$a->re_set('href','http://www.google.com');
	$a->re_set('title',"I'm now a sheep I in the doghouse. o_o");
	$a->re_set('text',"I'm now an easily herded simple minded sheep like everyone else. =( ");
	echo $a.$br.$br;
	
	$a->remove('href');
	$a->re_set('text',"I am now a dead link. =|");
	echo $a.$br.$br;
	
	$a->remove('text');
	$a->set('href','http://www.mamma.com/');
	$a->re_set('title',"You better good or mama will put you in the doghouse. o_~");
	$a->nest($img);
	echo 'Image link points to mother of all search engines: '.$a.$br.$br;
	
	$a->clear();
	echo 'I am now a dead image-link: '.$a.$br.$br;

}

class el {			# creates a very user-friendly html element, just create new element and echo it
	private $tag;		# the html element to create
	private $uni;		# the remaining (ten) non-deprecated w3c recognized self-closing unitags
	public $atts;		# attributes are entered into an associative array
	private $obj;		# the object to be nested
	
	public function __construct($tag,$atts=array(),$uni=array('meta','base','link','img','br','hr','param','input','option','col')){
		$this->tag = strtolower($tag); 
		$this->atts = $atts; 
		$this->uni = $uni;
		if($atts) {
			foreach($atts as $key => $val){
				$k = str_replace( array("\"","'"), array('',''), $key );
				$v = str_replace("\"","'",$val);
				$this->set($k,$v); 
			}
		}
	}
	
	public function __toString(){ # object emulates string after calling echo or print
		return $this->build();
	}
	
	public function get($key){# solicits a value for a given attribute
		if(property_exists($this,$key)) return $this->atts[$key];
		else echo "Cannot get attribute – \"$key\" is not a property of \"$this\".";
		return $this;
	}
	
	public function _($key,$val=''){ #same as set function
		# sets an attribute value, can pass array or key
		if(property_exists($this,$key)){
			echo 'Cannot set an existing property – instead use re_set.'; 
			return $this;
		}
		if(!is_array($key)) {
			$temp = array($key => $val);# make it into an array if its not an array
		}
		if($this->atts = array_merge($this->atts,$temp)){
			return $this;
		}
		else echo "Cannot merge supplied parameters with existing object $this.\n";
	}
	
	public function set($key,$val=''){
		# sets an attribute value, can pass array or key
		if(property_exists($this,$key)){
			echo 'Cannot set an existing property – instead use re_set.'; 
			return $this;
		}
		if(!is_array($key)) {
			$temp = array($key => $val);# make it into an array if its not an array
		}
		if($this->atts = array_merge($this->atts,$temp)){
			return $this;
		}
		else echo "Cannot merge supplied parameters with existing object $this.\n";
	}
	
	public function re_set($key,$val){# changes value of an attribute – not for resetting pointer
		if($this->remove($key)) $this->set($key,$val);
		else echo "Cannot change attribute, \"$key\" is not a property of \"$this\".";
		return $this;
	}
	
	public function remove($key){	# removes a single attribute
		if(isset($this->atts[$key])){unset($this->atts[$key]);}return $this;
	}
	
	public function clear(){		# clears all attributes
		$this->atts = array();
		return $this;
	}
	
	public function nest( $obj ){	# appends nested object
		if(get_class($obj) == __class__){
			$this->obj .= $obj->build();
		}
		return $this;
	}
	
	# build element and return text or but not text
	private function build(){			# and certainly not some text
		$el = "\n\t\t\t<".$this->tag;	# tag opening
		if(count($this->atts)) {		# attributes
			foreach($this->atts as $key=>$val){
				if($key!='text') { 
					$el.= ' '.$key.'="'.$val.'"';
				}
			}
		}
		if(in_array($this->tag,$this->uni)) {
			return $el.= " />\n";# return self-closing tag
		}
		if(!$this->obj) {
			return $el.= '>' . $this->atts['text'] ."</{$this->tag}".'>';	# return tag with inserted text
		}
		else {
			return $el.= '>'.$this->obj."</{$this->tag}".'>';# return paired tag with inserted object
		}
	}
	public function dumpy(){		# take a dump before pulling yer hair out
		echo "; var_dump($this); echo "; 
		return;
	}
	//public function update($obj){$this->obj = $obj; return $this;}# __constructor-like magic
}