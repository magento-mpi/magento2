<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Core\Model\Resource\Translate\String $translateString */
$translateString = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Core\Model\Resource\Translate\String');
$translateString->saveTranslate(
    'Fixture String', 'Fixture Db Translation', null, \Magento\Core\Model\AppInterface::ADMIN_STORE_ID
);
