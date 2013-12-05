<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Core\Model\App')
    ->loadAreaPart(
        \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
        \Magento\Core\Model\App\Area::PART_CONFIG
    );
$designDir = \Magento\TestFramework\Helper\Bootstrap::getInstance()->getAppInstallDir() . '/media_for_change';
$themeDir = $designDir . '/frontend/test_default';
$sourcePath = dirname(__DIR__) . '/Model/_files/design/frontend/test_publication/';

@mkdir($themeDir . '/images', 0777, true);

// Copy all files to fixture location
$mTime = time() - 10; // To ensure that all files, changed later in test, will be recognized for publication
$files = array('theme.xml', 'style.css', 'sub.css', 'images/square.gif', 'images/rectangle.gif');
foreach ($files as $file) {
    copy($sourcePath . $file, $themeDir . '/' . $file);
    touch($themeDir . '/' . $file, $mTime);
}

/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Theme\Registration');
$registration->register(
    $designDir,
    '*/*/theme.xml'
);
