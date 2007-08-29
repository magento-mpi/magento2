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
 * @package    Mage_Customer
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->addAttribute('customer', 'created_in', array(
	'type'=>'int',
	'label'=>'Created From',
	'input'=>'select',
	'source'=>'customer_entity/customer_attribute_source_store',
));
$this->addAttribute('customer', 'store_id', array(
	'type'=>'static',
	'label'=>'Create In',
	'input'=>'select',
	'source'=>'customer_entity/customer_attribute_source_store',
	'backend'=>'customer_entity/customer_attribute_backend_store',
));
