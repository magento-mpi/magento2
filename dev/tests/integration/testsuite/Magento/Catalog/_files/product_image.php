<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

$objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$mediaPath = $objectManager->get('Magento\App\Filesystem')->getPath(\Magento\App\Filesystem::MEDIA_DIR);
$additionalPath = $objectManager->get('Magento\Catalog\Model\Product\Media\Config')->getBaseMediaPath();
$dir = $mediaPath . '/' . $additionalPath . '/m/a';
if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}
copy(__DIR__ . '/magento_image.jpg', $dir . '/magento_image.jpg');
