<?php
	class crawler{
		private $url;
		static $a=0;
		static $crawled=array();
		function __construct($in_url)
		{
			$this->url=$in_url;
			crawler::$a++;
			echo '<b> <br>Instance '.crawler::$a.' Crawling : '.$this->url .'</b>';
			
		}
		function getdata(){
		 	return file_get_contents($this->url);
		}
		function get_content(){
			$new_ip=$this->getdata();
			$pos=strrpos($this->url,'/');
			$cat_name=substr($this->url,$pos+1);
			$cat_name=substr($cat_name,0,strlen($cat_name)-4);
			$doc = new DOMDocument('1.0');
			
			@$doc->loadHTMLFile($this->url);
			if(($new_div=$doc->getElementById($cat_name))===null)
					echo 'Nothing';
			else{
					echo '<b><br>subcategories:<br></b>';
					 $lists = $new_div->getElementsByTagName('li');
					 foreach($lists as $li){
							echo  '- -'.$li->nodeValue . "<br>";
					}
					echo '<b><br>Description:<br></b>';
					 $paras = $new_div->getElementsByTagName('p');
					 foreach($paras as $para){
							echo  $para->nodeValue . "<br>";
					}
					echo '<b><br>options:<br></b>';
					$dls = $new_div->getElementsByTagName('dl');
					 foreach($dls as $dl){
						echo  $dl->nodeValue . "<br>";
					}
					$divs = $new_div->getElementsByTagName('div');
					 foreach($divs as $dd){
						if($dd->getAttribute('class')=='example-contents screen')
								echo  $dd->nodeValue . "<br>";
					}
					echo '<b><br>Syntax :<br></b>';
					$table = $new_div->getElementsByTagName('table');
					 foreach($table as $td){
						echo  $td->nodeValue . "<br>";
					}
					
				}
		}
		function filter_data(){
	
			$new_ip=$this->getdata();
			$crwl=array();
			$i=0;
			 preg_match_all("/<a [^>]*href[\s]*=[\s]*\"([^\"]*)\"/i", $new_ip, $links);
			array_push(crawler::$crawled,$this->url);
			foreach($links[1] as $link){
					$parsed=parse_url($link);
					if(strpos($parsed['path'],'feature')!==false){
							if(isset($parsed['host']))
								$new_link='http://'.$parsed['host'].$parsed['path'];
							else
								$new_link='http://www.php.net/manual/en/'.$parsed['path'];
						$crwl[$i++]=$new_link;				
					}						
			}
			
			$this->get_content();
			foreach($crwl as $link){
				if(array_search($link,crawler::$crawled)===false){
					$rec_crw=new crawler($link);
					$rec_crw->filter_data();
				}
			}
		}
	}
	
	$new_crw= new crawler('http://www.php.net/manual/en/index.php');
	//$new_crw->getdata();
	$new_crw->filter_data();
?>
