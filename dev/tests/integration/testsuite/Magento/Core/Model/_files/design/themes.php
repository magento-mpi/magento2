<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
use Magento\Framework\App\Filesystem\DirectoryList;

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\AreaList')
    ->getArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
    ->load(\Magento\Framework\App\Area::PART_CONFIG);
\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
    DirectoryList::INIT_PARAM_PATHS => array(
        DirectoryList::THEMES => array('path' => realpath(__DIR__)),
    ),
));
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->configure(
    array('preferences' => array('Magento\Core\Model\Theme' => 'Magento\Core\Model\Theme\Data'))
);
/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Core\Model\Theme\Registration'
);
$registration->register(implode('/', array('*', '*', 'theme.xml')));
