<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/** @var Magento\GiftWrapping\Model\Wrapping $wrapping */
$wrapping = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\GiftWrapping\Model\Wrapping');
$wrapping->setDesign('Test Wrapping')
    ->setStatus(1)
    ->setBasePrice(5.00)
    ->setImage('image.png')
    ->save();
