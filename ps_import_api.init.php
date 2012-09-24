<?php
/*
* 2007-2011 PrestaShop 
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2011 PrestaShop SA
*  @version  Release: $Revision: 7588 $
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

$timerStart = microtime(true);

$currentFileName = array_reverse(explode("/", $_SERVER['SCRIPT_NAME']));
$cookieLifetime = (time() + (((int)Configuration::get('PS_COOKIE_LIFETIME_BO') > 0 ? (int)Configuration::get('PS_COOKIE_LIFETIME_BO') : 1)* 3600));
$cookiePath= substr($_SERVER['SCRIPT_NAME'], strlen(__PS_BASE_URI__), -strlen($currentFileName['0']));
$cookie = new Cookie('psAdmin', $cookiePath, $cookieLifetime);

$currentIndex = $_SERVER['SCRIPT_NAME'].(($tab = Tools::getValue('tab')) ? '?tab='.$tab : '');
if ($back = Tools::getValue('back'))
	$currentIndex .= '&back='.urlencode($back);

/* Server Params */
$protocol_link = (Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
$protocol_content = (isset($useSSL) AND $useSSL AND Configuration::get('PS_SSL_ENABLED')) ? 'https://' : 'http://';
define('_PS_BASE_URL_', Tools::getShopDomain(true));
define('_PS_BASE_URL_SSL_', Tools::getShopDomainSsl(true));

$cookie->id_lang = $cookie->id_lang ? $cookie->id_lang : Configuration::get('PS_LANG_DEFAULT');
$iso = strtolower(Language::getIsoById($cookie->id_lang));
include(_PS_TRANSLATIONS_DIR_.$iso.'/errors.php');
include(_PS_TRANSLATIONS_DIR_.$iso.'/fields.php');
include(_PS_TRANSLATIONS_DIR_.$iso.'/admin.php');
define('_USER_ID_LANG_', (int)$cookie->id_lang);

