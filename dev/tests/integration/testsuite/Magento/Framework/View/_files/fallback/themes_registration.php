<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
use Magento\Framework\App\Bootstrap;
use Magento\Framework\App\Filesystem\DirectoryList;

\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
    Bootstrap::INIT_PARAM_FILESYSTEM_DIR_PATHS => array(
        DirectoryList::THEMES => array('path' => __DIR__ . '/design')
    )
));
$objectManger = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = $objectManger->create('Magento\Core\Model\Theme\Registration');
$registration->register('*/*/theme.xml');
