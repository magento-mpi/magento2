<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\App\AreaList')
    ->getArea(\Magento\Backend\App\Area\FrontNameResolver::AREA_CODE)
    ->load(\Magento\Core\Model\App\Area::PART_CONFIG);
/** @var \Magento\Core\Model\Resource\Translate\String $translateString */
$translateString = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Resource\Translate\String');
$translateString->saveTranslate('string to translate', 'predefined string translation', null);
