<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    tools
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
 /*
 
php -f generate.php -- --locale en_US --output filename

Output file format (CSV):
Columns:

Module_Name (like 'Mage_Catalog' or design package name like 'translate')
Translation Key (like "Translate Me")
Translation Value (the same)
Source File (source file name)
Line # (line #)


Patterns:



Mage::helper('helper_name') => Module_Name
$this->__() => Used Module Name if found setUsedModuleName('name') in file, otherwise use Module_Name from config
__() => translate

'helpers' = array(
    'core' => 'Mage_Core',
    'core' => 'Mage_Core',
);

 */