<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_Core_Model_Resource_Translate_String $translateString */
$translateString = Magento_TestFramework_Helper_Bootstrap::getObjectManager()
    ->create('Magento_Core_Model_Resource_Translate_String');
$translateString->saveTranslate('Fixture String', 'Fixture Db Translation');
