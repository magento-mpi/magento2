<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $block Magento_Cms_Model_Block */
$block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
            ->create('Magento_Cms_Model_Block');
$block->setTitle('CMS Block Title')
    ->setIdentifier('fixture_block')
    ->setContent('<h1>Fixture Block Title</h1>
<a href="{{store url=""}}">store url</a>
<p>Config value: "{{config path="web/unsecure/base_url"}}".</p>
<p>Custom variable: "{{customvar code="variable_code"}}".</p>
')
    ->setIsActive(1)
    ->setStores(array(Mage::app()->getStore()->getId()))
    ->save()
;
