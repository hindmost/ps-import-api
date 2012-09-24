<?php
/*
* PrestaShop Import API
*
*  @version 0.9.1
*  @author Savr Goryaev
*  @copyright  2007-2011 PrestaShop SA <contact@prestashop.com>
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*   International Registered Trademark & Property of PrestaShop SA
*
*  Note: tested on PrestaShop v1.4.x
*/


class PSImportAPI
{
	static protected function prepare($entity= 1) {
		$_GET['entity']= $_POST['entity']= $entity;
	}
	
	/*
	* Add/update category
	* @param array $info - category info, array of name->value pairs
	* @return integer - category ID on success
	* @return boolean - false on failure
	*/
	static function putCategory($info) {
		self::prepare(0);
		$import= new AdminImport();
		$id= $import->categoryImportRow($info);
		return $id;
	}

	/*
	* Add/update product
	* @param array $info - product info, array of name->value pairs
	* @param array $features - product features, array of name->value pairs
	* @param boolean $bAddParentCategories
	* @return integer - product ID on success
	* @return boolean - false on failure
	*/
	static function putProduct($info, $features= 0, $bAddParentCategories= false) {
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
		self::prepare(1);
		$import= new AdminImport();
		$id= $import->productImportRow($info, $features, $bAddParentCategories);
		return $id;
	}

	/*
	* Update product. Lite/fast version of "putProduct" method (update case)
	* @param integer $id - product ID
	* @param array $info - product info, array of name->value pairs
	* @param array $categories - product categories (IDs), array of integers
	* @param boolean $bCheckId - checks product ID for existence in database
	* @return integer - product ID on success
	* @return boolean - false on failure
	*/
	public function updateProduct($id, $info, $categories= 0, $bCheckId= false)
	{
		$id= (int)($id);
		if ($bCheckId && !Product::existsInDatabase($id)) return false;
		$info['date_upd'] = date('Y-m-d H:i:s');
		$ret = Db::getInstance()->autoExecute(_DB_PREFIX_.'product', $info, 'UPDATE', '`id_product` = '.$id);
		if (is_array($categories) && count($categories)) {
			$product = new Product($id);
			$b_retcats= $product->addToCategories(array_map('intval', $categories));
		}
		return $ret? $id: false;
	}

	/*
	* Search product by its reference
	* @param string $reference - value of reference
	* @return integer - product ID on success
	* @return boolean - false on failure
	*/
	static function getProductByReference($reference) {
		if (!(is_numeric($reference) || $reference && Validate::isReference($reference)))
			return false;
		$sql= "SELECT `id_product` FROM `"._DB_PREFIX_."product` WHERE `reference` = '$reference'";
		$id= Db::getInstance()->getValue($sql, false);
		return $id;
	}

	/*
	* Generate categories tree for right breadcrumbs displaying.
	* Must be called on the end of import
	*/
	static function finishImport() {
		Category::regenerateEntireNtree();
	}
}
?>