<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\Translate\Model\Resource\String $translateString */
$translateString = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
    ->create('Magento\Translate\Model\Resource\Translate\String');
$translateString->saveTranslate('Fixture String', 'Fixture Db Translation');
