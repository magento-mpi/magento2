<?php

require_once('config.inc.php');

/*

Usage:

##########################################################################################

$> translate.php -path ~/dev/magento/ -validate ru_RU

# Validates selected translation against the default (en_US)
# - checks for missing, redundant or duplicate keys

# Output example:

Mage_Adminhtml.csv:
    "My Wishlist" => missing
    "Report All Bugs" => missing
    "My Account" => redundant (137)
Mage_Catalog.csv:
    "Product Title" => redundant (245)
    "Attributes" => duplicate (119, 235, 307)

##########################################################################################

$> translate.php -path ~/dev/magento/ -generate [-file Mage_Adminhtml] [-file Mage_Catalog]

# Generates the default translation (en_US)

# Output example:

Created diffs:
    Mage_Adminhtml.1743-1802.csv
    Mage_Catalog.1747-1802.csv

Updated files:
    Mage_Adminhtml.csv
    Mage_Catalog.csv

##########################################################################################

$> translate.php -path ~/dev/magento/ -update ru_RU [-file Mage_Adminhtml] [-file Mage_Catalog]

# Updates the selected translation with the changes (if any) from the default one (en_US)

# Output example:

Created diffs:
    Mage_Adminhtml.1743-1802.csv
    Mage_Catalog.1747-1802.csv

Updated files:
    Mage_Adminhtml.csv
    Mage_Catalog.csv

##########################################################################################

$> translate.php -path ~/dev/magento/ -dups [-key "Checkout"]

# Checks for duplicate keys in different modules (in the default translation en_US)

# Output example:

"Checkout":
   Mage_Adminhtml.csv (1472) from app/code/core/Mage/Adminhtml/Block/Widget/Grid/Container.php (46)
   Mage_Catalog.csv (723) from design/frontend/default/default/catalog/product/view.phtml (172)


*/