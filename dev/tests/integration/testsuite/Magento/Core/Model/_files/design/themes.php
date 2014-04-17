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
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\Framework\App\AreaList')
    ->getArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
    ->load(\Magento\Framework\App\Area::PART_CONFIG);
\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
    \Magento\Framework\App\Filesystem::PARAM_APP_DIRS => array(
        \Magento\Framework\App\Filesystem::THEMES_DIR => array('path' => realpath(__DIR__)),
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
