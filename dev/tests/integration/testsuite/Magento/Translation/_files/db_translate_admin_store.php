<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
    'Magento\Framework\App\AreaList'
)->getArea(
    \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE
)->load(
    \Magento\Framework\App\Area::PART_CONFIG
);
/** @var \Magento\Translation\Model\Resource\String $translateString */
$translateString = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Translation\Model\Resource\String'
);
$translateString->saveTranslate('string to translate', 'predefined string translation', null);
