<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var $block \Magento\Cms\Model\Block */
$block = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Cms\Model\Block');
$block->setTitle(
    'CMS Block Title'
)->setIdentifier(
    'fixture_block'
)->setContent(
    '<h1>Fixture Block Title</h1>
<a href="{{store url=""}}">store url</a>
<p>Config value: "{{config path="web/unsecure/base_url"}}".</p>
<p>Custom variable: "{{customvar code="variable_code"}}".</p>
'
)->setIsActive(
    1
)->setStores(
    array(
        \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\StoreManagerInterface'
        )->getStore()->getId()
    )
)->save();
