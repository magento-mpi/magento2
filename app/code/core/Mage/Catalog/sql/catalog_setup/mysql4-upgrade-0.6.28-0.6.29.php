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
 * @package    Mage_Catalog
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

$this->cleanCache();

$this->addAttribute('catalog_category', 'url_path', array(
    'type'      => 'varchar',
    'label'     => '',
    'frontend'  => '',
    'table'     => '',
    'label'     => '',
    'input'     => '',
    'class'     => '',
    'source'    => '',
    'global'    => false,
    'visible'   => false,
    'required'  => false,
    'user_defined' => false,
    'default'   => '',
    'searchable'=> false,
    'filterable'=> false,
    'comparable'=> false,
    'visible_on_front' => false,
    'unique'    => true,
));

#Mage::getModel('catalog/url')->refreshRewrites();