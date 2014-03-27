<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Translation\Model\Resource\String $translateString */
$translateString = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
    'Magento\Translation\Model\Resource\String'
);
$translateString->saveTranslate('Fixture String', 'Fixture Db Translation');
