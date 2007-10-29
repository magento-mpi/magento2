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
 * @package    Mage_Poll
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->run("
delete from `core_config_field` where `path` like 'payment/paypal/%';
delete from `core_config_data` where `path` like 'payment/paypal/%';

drop table if exists paypal_api_debug;
CREATE TABLE `paypal_api_debug` (
  `debug_id` int(10) unsigned NOT NULL auto_increment,
  `debug_at` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `request_body` text,
  `response_body` text,
  PRIMARY KEY  (`debug_id`),
  KEY `debug_at` (`debug_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$this->addConfigField('paypal', 'PayPal');


// PAYPAL WPP
$this->addConfigField('paypal/wpp', 'Website Payments Pro');
$this->addConfigField('paypal/wpp/sandbox_flag', 'Sandbox Flag', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));

$this->addConfigField('paypal/wpp/api_username', 'API User Name');
$this->addConfigField('paypal/wpp/api_password', 'API Password');
$this->addConfigField('paypal/wpp/api_signature', 'API Signature');

$this->addConfigField('paypal/wpp/use_proxy', 'Use Proxy', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));
$this->addConfigField('paypal/wpp/proxy_host', 'Proxy Host');
$this->addConfigField('paypal/wpp/proxy_port', 'Proxy Port');


// PAYPAL EXPRESS
$this->addConfigField('payment/paypal_express', 'Paypal Express');

$this->setConfigData('payment/paypal_express/model', 'paypal/express');

$this->addConfigField('payment/paypal_express/active', 'Enabled', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));

$this->addConfigField('payment/paypal_express/title', 'Title');

$this->addConfigField('payment/paypal_express/order_status', 'New order status', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_order_status',
));

$this->addConfigField('payment/paypal_express/sort_order', 'Sort order');

// PAYPAL DIRECT
$this->addConfigField('payment/paypal_direct', 'PayPal Direct');

$this->setConfigData('payment/paypal_direct/model', 'paypal/direct');

$this->addConfigField('payment/paypal_direct/active', 'Enabled', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_yesno',
));

$this->addConfigField('payment/paypal_direct/title', 'Title');

$this->addConfigField('payment/paypal_direct/order_status', 'New order status', array(
    'frontend_type'=>'select',
    'source_model'=>'adminhtml/system_config_source_order_status',
));

$this->addConfigField('payment/paypal_direct/sort_order', 'Sort order');

$this->installEntities($this->getDefaultEntities());