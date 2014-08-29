<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento\GiftWrapping\Model\Wrapping $wrapping */
$wrapping = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\GiftWrapping\Model\Wrapping');
$wrapping->setDesign('Test Wrapping')
    ->setStatus(1)
    ->setBasePrice(5.00)
    ->setImage('image.png')
    ->save();
