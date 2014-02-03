<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
\Magento\TestFramework\Helper\Bootstrap::getInstance()->reinitialize(array(
    \Magento\App\Filesystem::PARAM_APP_DIRS => array(
        \Magento\App\Filesystem::THEMES_DIR => array('path' => dirname(__DIR__) . '/_files/design')
    )
));
$objectManger = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
$objectManger->get('Magento\Core\Model\App')
    ->loadAreaPart(
        \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE,
        \Magento\Core\Model\App\Area::PART_CONFIG
    );
$objectManger->configure(array(
    'preferences' => array(
        'Magento\Core\Model\Theme' => 'Magento\Core\Model\Theme\Data'
    )
));
/** @var $registration \Magento\Core\Model\Theme\Registration */
$registration = $objectManger->create('Magento\Core\Model\Theme\Registration');
$registration->register(
    implode('/', array('*', '*', 'theme.xml'))
);
