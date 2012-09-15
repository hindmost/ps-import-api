<?php
require_once "tool-tracelog.inc";

define('FNAME_PATCH', 'ps_import_api.patch.txt'	);
define('TOKEN_PATCHED', 'productImportRow('	);
define('TOKEN_CLASSEND', '}'	);

function patch($fnameObj, $fnamePatch) {
	traceAdd("patch(): fnameObj='$fnameObj'; fnamePatch='$fnamePatch'\n");
	if (!$fnameObj || !file_exists($fnameObj)) return false;
	$cont_obj= file_get_contents($fnameObj);
	traceAdd(" len(cont_obj)=".strlen($cont_obj)."\n");
	if (strpos($cont_obj, TOKEN_PATCHED)!==false) return 2;
	if (!($k= strrpos($cont_obj, TOKEN_CLASSEND))) return false;
	traceAdd(" k=$k\n");
	if (!$fnamePatch || !file_exists($fnamePatch)) return false;
	$cont_patch= file_get_contents($fnamePatch);
	traceAdd(" len(cont_patch)=".strlen($cont_patch)."\n");
	if (preg_match('/^<[?](?:php|)/', $cont_patch, $arr) && is_array($arr))
		$cont_patch= substr($cont_patch, strlen($arr[0]));
	$cont_obj= substr($cont_obj, 0, $k). $cont_patch. substr($cont_obj, $k);
	$cont_obj= untrace($cont_obj);
	traceAdd(" len(cont_obj)-2=".strlen($cont_obj)."\n len(cont_patch)-2=".strlen($cont_patch)."\n");
	file_put_contents($fnameObj, $cont_obj);
	return 1;
}

function untrace($text) {
	$b_wincr= strpos($text, "\r\n");
	$text= str_replace("\r\n", "\n", $text);
	$a_find= array(
		"(^|\n)[ \t]*(?:[^\n]+[; \t]|)trace[^\n]+",
		"(^|\n)[^\n]+tool\-tracelog\.inc[^\n]+",
	);
	foreach ($a_find as $s)
		$text= preg_replace("/".$s."/is", "", $text);
	if ($b_wincr) $text= str_replace("\n", "\r\n", $text);
	return $text;
}

traceInit('log/trace-rsv-import-install.log');

$fname= 'AdminImport.php';
if (isset($_REQUEST['filename']) && ($v= $_REQUEST['filename']))
	$fname= $v;
if ($argc>=2 && ($v= trim($argv[1])))
	$fname= $v;

$ret= patch($fname, FNAME_PATCH);
traceFlush();

switch ($ret) {
case 1:
	echo 'Installation completed: file "'.$fname.'" successfully patched'; break;
case 2:
	echo 'Installation has already done'; break;
default:
	echo 'Installation failed';
}
?>