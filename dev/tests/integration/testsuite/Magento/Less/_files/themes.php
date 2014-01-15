<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
    \Magento\Filesystem::PARAM_APP_DIRS => array(
        \Magento\Filesystem::THEMES => array('path' => __DIR__ . '/design')
    )
));
$objectManger = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();

/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = $objectManger->create('Magento\Core\Model\Theme\Registration');
$registration->register('*/*/theme.xml');
