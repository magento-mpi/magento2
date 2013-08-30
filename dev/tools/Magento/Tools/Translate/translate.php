<?php
/**
 * {license_notice}
 *
 * @category   Tools
 * @package    translate
 * @copyright  {copyright}
 * @license    {license_link}
 */

define( 'USAGE', <<<EOT

USAGE:

################################################################################

$> php -f translate.php -- --path ~/dev/magento/ --validate ru_RU [--file Magento_Adminhtml] [--file Magento_Catalog]

# Validates selected translation against the default (en_US)
# - checks for missing, redundant or duplicate keys

# missing - not present in default (english) csv, but present in validated file.
# redundant - not present in validated file, but present in default (english) csv
# duplicate - duplication in validated file

# Output example:

Magento_Adminhtml.csv:
    "My Wishlist" => missing
    "Report All Bugs" => missing
    "My Account" => redundant (137)
Magento_Catalog.csv:
    "Product Title" => redundant (245)
    "Attributes" => duplicate (119, 235, 307)

################################################################################

$> php -f translate.php -- --path ~/dev/magento/ --generate [--file Magento_Adminhtml] [--file Magento_Catalog]

# Generates the default translation (en_US)

# missing - present in locale but not present in module
# redundant - present in module but not present in locale
# duplicate - duplication in module

# Output example:

Created diffs:
    Magento_Adminhtml.1743-1802.csv
    Magento_Catalog.1747-1802.csv

Updated files:
    Magento_Adminhtml.csv
    Magento_Catalog.csv

################################################################################

$> php -f translate.php -- --path ~/dev/magento/ --update ru_RU [--file Magento_Adminhtml] [--file Magento_Catalog]

# Updates the selected translation with the changes (if any) from the default one (en_US)

# Output example:

Created diffs:
    Magento_Adminhtml.1743-1802.csv
    Magento_Catalog.1747-1802.csv

Updated files:
    Magento_Adminhtml.csv
    Magento_Catalog.csv

################################################################################

$> php -f translate.php -- --path ~/dev/magento/ --dups [--key "Checkout"]

# Checks for duplicate keys in different modules (in the default translation en_US)

# Output example:

"Checkout":
   Magento_Adminhtml.csv (1472) from app/code/Mage/Adminhtml/Block/Widget/Grid/Container.php (46)
   Magento_Catalog.csv (723) from design/frontend/default/default/catalog/product/view.phtml (172)

################################################################################

EOT
);

define('DS', DIRECTORY_SEPARATOR);
define('PS', PATH_SEPARATOR);
define('BASE_PATH', dirname(dirname(dirname(__DIR__))));

ini_set('include_path', ini_get('include_path')
    . PS . BASE_PATH. DS . 'lib'
);

require_once __DIR__ . '/config.inc.php';
require_once __DIR__ . '/MultyGetopt.php';
require_once 'Varien/File/CsvMulty.php';
require_once __DIR__ . '/DirectoryFilter.php';

require_once 'Varien/Simplexml/Config.php';
require_once 'Varien/Simplexml/Element.php';

Magento_Tools_Translate_Translate::run($CONFIG);
