<?php
	//get old stuff
	$ID = $_POST['ID'];
	$coauth = $_POST['coauth'];
	$inc = $_POST['inc'];
	
	//read source-file
	
	if(strpos($ID, ':') !== false) {
		@list($name, $page) = explode(':',$ID,2);
		$f = fopen($inc.'data/pages/'.$name.'/'.$page.'.txt', 'r+');
	} else {
		$f = fopen($inc.'data/pages/'.$ID.'.txt', 'r+');
	}
	
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
		
		header("Location: /doku.php?id=".$ID );
		die();
	}
	
