<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Creates banner with enabled status and text content
 */

/** @var $banner \Magento\Banner\Model\Banner */
$banner = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Banner\Model\Banner');

$banner->setIsEnabled(
    \Magento\Banner\Model\Banner::STATUS_ENABLED
)->setName(
    'Test Banner'
)->setTypes(
    ''
)->setStoreContents(
    [0 => 'Banner Content']
)->save();
