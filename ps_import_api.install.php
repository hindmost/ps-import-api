<?php
define('FILE_PATCH', 'ps_import_api.patch.txt'	);
define('FIND_PATCHED', 'productImportRow('	);
define('FIND_CLASSEND', '}'	);

function patch($fnameObj, $fnamePatch) {
	if (!$fnameObj || !file_exists($fnameObj)) return false;
	$cont_obj= file_get_contents($fnameObj);
	if (strpos($cont_obj, FIND_PATCHED)!==false) return 2;
	if (!($k= strrpos($cont_obj, FIND_CLASSEND))) return false;
	if (!$fnamePatch || !file_exists($fnamePatch)) return false;
	$cont_patch= file_get_contents($fnamePatch);
	if (preg_match('/^<[?](?:php|)/', $cont_patch, $arr) && is_array($arr))
		$cont_patch= substr($cont_patch, strlen($arr[0]));
	$cont_obj= substr($cont_obj, 0, $k). $cont_patch. substr($cont_obj, $k);
	file_put_contents($fnameObj, $cont_obj);
	return 1;
}


$fname= 'AdminImport.php';
if (isset($_REQUEST['filename']) && ($v= $_REQUEST['filename']))
	$fname= $v;
if ($argc>=2 && ($v= trim($argv[1])))
	$fname= $v;

$ret= patch($fname, FILE_PATCH);

switch ($ret) {
case 1:
	echo 'Installation completed: file "'.$fname.'" successfully patched'; break;
case 2:
	echo 'Installation has already done'; break;
default:
	echo 'Installation failed';
}
?>