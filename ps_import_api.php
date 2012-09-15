<?php
/*
* PrestaShop Import Interface
*
*  @version 0.9
*  @author Savr Goryaev
*  @copyright  2007-2011 PrestaShop SA <contact@prestashop.com>
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*   International Registered Trademark & Property of PrestaShop SA
*
*  Note: tested on PrestaShop v1.4.4.0 Russian (http://prestadev.ru/)
*/


class PSImportAPI
{
	static protected function prepare($entity= 1) {
		traceAdd("PSImportAPI::prepare($entity):\n\n");
		$_GET['entity']= $_POST['entity']= $entity;
	}
	
	static function putCategory($info) {
		self::prepare(0);
		$import= new AdminImport();
		$id= $import->categoryImportRow($info);
		return $id;
	}

	static function putProduct($info, $features= 0, $bParentCategories= true) {
		static $sep= '';
		if (!$sep) $sep= 
			(is_null(Tools::getValue('multiple_value_separator')) OR trim(Tools::getValue('multiple_value_separator')) == '' )?
			',' : Tools::getValue('multiple_value_separator');
		if (!is_array($info)) return false;
		if (isset($info['category'])) {
			$r_cats= &$info['category'];
			if (is_array($r_cats)) $r_cats= implode($sep, $r_cats);
		}
		if (isset($info['tags'])) {
			$r_tags= &$info['tags'];
			if (is_array($r_tags)) $r_tags= implode($sep, $r_tags);
		}
		if (isset($info['image'])) {
			$r_imgs= &$info['image'];
			if (is_array($r_imgs)) $r_imgs= implode($sep, $r_imgs);
		}
		traceAdd("PSImportAPI::putProduct('$bParentCategories'): sep='$sep'\n");
		self::prepare(1);
		$import= new AdminImport();
		$id= $import->productImportRow($info, $features, $bParentCategories);
		return $id;
	}

	public function updateProduct($id, $info)
	{
		$info['date_upd'] = date('Y-m-d H:i:s');
		$ret = Db::getInstance()->autoExecuteWithNullValues(_DB_PREFIX_.'product', $info, 'UPDATE', '`id_product` = '.(int)($id));
		traceAdd("PSImportAPI::updateProduct(): id='$id'\n info: ".traceGetObj($info)."\n ret='$ret'\n");
		return $ret? $id: false;
	}

	static function getProductByReference($reference) {
		if (!(is_numeric($reference) || $reference && Validate::isReference($reference)))
			return false;
		$sql= "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '$reference'";
		$id= Db::getInstance()->getValue($sql, false);
		traceAdd("PSImportAPI::getProductByReference('$reference'): id='$id'\n");
		return $id;
	}

	static function finishImport($reference) {
		Category::regenerateEntireNtree();
	}
}
?>