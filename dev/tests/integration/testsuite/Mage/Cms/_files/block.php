<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$block = new Mage_Cms_Model_Block;
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
