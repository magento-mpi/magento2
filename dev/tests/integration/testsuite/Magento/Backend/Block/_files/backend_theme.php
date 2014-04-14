<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var $registration \Magento\Core\Model\Theme\Registration */
\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\AreaList')
    ->getArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
    ->load(\Magento\App\Area::PART_CONFIG);
$registration = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Theme\Registration');
$registration->register(
    __DIR__ . '/design',
    '*/*/theme.xml'
);
