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

/** @var $block \Magento\Cms\Model\Block */
$block = \Mage::getModel('Magento\Cms\Model\Block');
$block->setTitle('CMS Block Title')
    ->setIdentifier('fixture_block')
    ->setContent('<h1>Fixture Block Title</h1>
<a href="{{store url=""}}">store url</a>
<p>Config value: "{{config path="web/unsecure/base_url"}}".</p>
<p>Custom variable: "{{customvar code="variable_code"}}".</p>
')
    ->setIsActive(1)
    ->setStores(array(\Mage::app()->getStore()->getId()))
    ->save()
;
