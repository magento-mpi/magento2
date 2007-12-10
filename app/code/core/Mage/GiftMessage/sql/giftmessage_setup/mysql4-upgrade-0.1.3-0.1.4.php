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
$this->startSetup();
$this->addAttribute('catalog_product', 'gift_message_aviable', array(
    'type'      => 'int',
    'backend'   => 'giftmessage/entity_attribute_backend_boolean_config',
    'frontend'  => '',
    'label'     => 'Allow Gift Message',
    'input'     => 'select',
    'class'     => '',
    'source'    => 'giftmessage/entity_attribute_source_boolean_config',
    'global'    => true,
    'visible'   => true,
    'required'  => false,
    'user_defined' => false,
    'default'   => '2',
    'visible_on_front' => false
));
$this->endSetup();