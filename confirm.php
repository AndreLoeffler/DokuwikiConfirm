<?php
	//get old stuff
	$ID = $_POST['ID'];
	$coauth = $_POST['coauth'];
	
	//read source-file
	$f = fopen('/home/andre/vhosts/wiki.bp/web/data/pages/'.$ID.'.txt', 'r+');
	
	//container array for matched patterns
	$matches = array();
	$i = 0; //counts the lines 
	
	//try to read it
	if($f) {
		while(!feof($f)) {
			$buffer = fgets($f, 4096);
			if(preg_match('/{{conf>'.$coauth.'(.*)}}/', $buffer, $matches)) {
				break;
			}
			$i++;
		}
	}
	
	//build replacement-string
	$repl = "{{conf>".$coauth."|c}}";

	//rewind and go to matched line	
	rewind($f);
	$j = 0;
	while($j < $i) {
		$buffer = fgets($f, 4096);
		$j++;
	}
	
	//write replacement at position
	fputs($f, $repl);
	
