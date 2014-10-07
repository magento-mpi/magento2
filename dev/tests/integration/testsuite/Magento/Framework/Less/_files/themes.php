<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
use Magento\Framework\App\Filesystem\DirectoryList;

\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(
    array(
        DirectoryList::PARAM_APP_DIRS => array(
            DirectoryList::THEMES_DIR => array('path' => __DIR__ . '/design')
        )
    )
);
$objectManger = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManger->get('Magento\Framework\App\State')
    ->setAreaCode(\Magento\Framework\View\DesignInterface::DEFAULT_AREA);

/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = $objectManger->create('Magento\Core\Model\Theme\Registration');
$registration->register('*/*/theme.xml');
