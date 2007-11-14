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
 * @package    Mage_GiftMessage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->addAttribute('quote', 'gift_message_id', array(
        'type'   =>'int',
        'visible'=>0,
        'required'=>0
));

$this->addAttribute('quote_address', 'gift_message_id', array(
        'type'   =>'int',
        'visible'=>0,
        'required'=>0
));

$this->addAttribute('quote_item', 'gift_message_id', array(
        'type'   =>'int',
        'visible'=>0,
        'required'=>0
));

$this->addAttribute('order', 'gift_message_id', array(
        'type'   =>'int',
        'visible'=>0,
        'required'=>0
));

$this->addAttribute('order_address', 'gift_message_id', array(
        'type'   =>'int',
        'visible'=>0,
        'required'=>0
));

$this->addAttribute('order_item', 'gift_message_id', array(
        'type'   =>'int',
        'visible'=>0,
        'required'=>0
));

$this->addAttribute('catalog_product', 'gift_message_aviable', array(
    'type'      => 'int',
    'backend'   => '',
    'frontend'  => '',
    'label'     => 'Allow Gift Message',
    'input'     => 'boolean',
    'class'     => '',
    'source'    => '',
    'global'    => true,
    'visible'   => true,
    'required'  => false,
    'user_defined' => false,
    'default'   => '1',
    'visible_on_front' => false
));


$this->addAttribute('catalog_product', 'gift_message_aviable', array(
    'type'      => 'int',
    'backend'   => '',
    'frontend'  => '',
    'label'     => 'Allow Gift Message',
    'input'     => 'boolean',
    'class'     => '',
    'source'    => '',
    'global'    => true,
    'visible'   => true,
    'required'  => false,
    'user_defined' => false,
    'default'   => '1',
    'visible_on_front' => false
));

$this->addConfigField('sales/gift_messages','Gift Messages');

$this->addConfigField('sales/gift_messages/allow', 'Allow Gift Messages', array(
    'frontend_type' =>  'select',
    'source_model'  =>  'adminhtml/system_config_source_yesno'
));