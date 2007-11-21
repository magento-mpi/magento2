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
 * @package    Mage_Payment
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
$this->updateTable('core/config_field', 
    "path like 'payment/%' AND source_model='adminhtml/system_config_source_payment_active'",
    "source_model='adminhtml/system_config_source_yesno'");

$this->addConfigField('payment/ccsave/cctypes', 'Credit Card Types', array(
    'frontend_type' => 'multiselect',
    'source_model'  => 'adminhtml/system_config_source_payment_cctype',
    'sort_order'    => 4
));

$this->addConfigField('payment/ccsave/useccv', 'Credit Card Verification', array(
    'frontend_type' => 'select',
    'source_model'  => 'adminhtml/system_config_source_yesno',
    'sort_order'    => 5
));