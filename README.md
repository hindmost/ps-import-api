# PrestaShop Import API

Provides interface to set of functions that implements import operations in PrestaShop (http://prestashop.com/).

Intended for use in a web scraping scripts (plugins) for PrestaShop.

Tested on PrestaShop v1.4.x.


## INSTALLATION

Put all .php files in your plugin's directory, within main directory of PrestaShop.

Execute script "ps_import_api.install.php" in one the following formats through console (1), or browser (2).

Format (1):
``` bash
PLUGIN_PATH/ps_import_api.install.php ADMIN_FILE_PATH
```

Format (2):
``` bash
PLUGIN_URL/ps_import_api.install.php?filename=ADMIN_FILE_PATH
```

Notations:

* PLUGIN_PATH - path to your plugin's directory.
* PLUGIN_URL - URL (+path) to your plugin's directory.
* ADMIN_FILE_PATH - relative path to the file "AdminImport.php" (one of the PrestaShop Admin source files), from your plugin's directory.

Examples:

1)
``` bash
ps_import_api.install.php ../tabs/AdminImport.php
```

2)
``` bash
http://yoursite.com/scraper/ps_import_api.install.php?filename=../tabs/AdminImport.php
```


## USAGE SAMPLE

Assume that the plugin's directory is in the PrestaShop Admin directory (e.g. "/admin/scraper/").

``` php

define('PS_ADMIN_DIR', getcwd().'/..');
include PS_ADMIN_DIR.'/../config/config.inc.php';
require_once 'ps_import_api.init.php';
require_once PS_ADMIN_DIR.'/tabs/AdminImport.php';
require_once 'ps_import_api.php';

...

/* Add Category: */
$a_info= array('name'=> $catname, 'parent'=> $id_parent);
$id_cat= PSImportAPI::putCategory($a_info);

...

/* Add Product: */
$a_info= array(
  'reference'=> $ref,
  'name'=> $name,
  'image'=> $url_image,
  'description'=> $desc,
  'quantity'=> $n_q,
  'price'=> $n_price,
  'manufacturer'=> $brand,
);
$a_features= array(
  'Brutto Weight'=> $brutto,
  'Material'=> $material,
);
$a_info['category']= array($id_cat);
$id_product= PSImportAPI::putProduct($a_info, $a_features);

...

/* Update Product: */
$a_info= array(
  'quantity'=> $n_q,
  'price'=> $n_price,
);
$id_product= PSImportAPI::updateProduct($id_product, $a_info, array($id_cat));

...

/* This method must (may) be called on the end of import process: */
PSImportAPI::finishImport();

```
