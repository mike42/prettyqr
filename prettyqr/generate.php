#!/usr/bin/php -q
<?php
/*	Command line interface to PrettyQR PHP library
	Copyright (C) 2012 Michael Billington <michael.billington@gmail.com>
*/
require_once("prettyqr.php");
/* Parse command line arguments */
$arg = array(	"--logo"	=> "",
		"--tile"	=> "rounded",
		"--text"	=> "",
		"--output"	=> "");
$arg_unlabelled = array(1 =>	'--text',
			2 =>	'--output');
$waiting = "";
$num = 0;
if(!isset($argv)) { die("This script must be launched from the command line"); }
foreach($argv as $val) {
	if(isset($arg[$val])) {
		$arg[$waiting] = true;
		$waiting = $val;
	} elseif($waiting != "") {
		$arg[$waiting] = $val;
		$waiting = "";
	} else {
		if(isset($arg_unlabelled[$num])) {
			/* Program name is not important */
			$tag = $arg_unlabelled[$num];
			$arg[$tag] = $val;
		}
		$num++;
	}
}
/* If not enough info */
if($arg['--text'] == "" || $arg['--output'] == "") {
	die("Usage: ".$argv[0]." \"text\" file.png [--logo file.png | --tile type]\n");
}

PrettyQR::generate($arg['--output'], $arg['--text'], $arg['--logo'], $arg['--tile']);
?>
