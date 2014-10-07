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
            DirectoryList::THEMES_DIR => array('path' => dirname(__DIR__) . '/design')
        )
    )
);
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->get('Magento\Framework\App\State')
    ->setAreaCode(\Magento\Framework\View\DesignInterface::DEFAULT_AREA);

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\AreaList')
    ->getArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
    ->load(\Magento\Framework\App\Area::PART_CONFIG);

/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Core\Model\Theme\Registration'
);
$registration->register('*/*/theme.xml');
