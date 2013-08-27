<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Magento_Core_Model_Resource_Translate_String $translateString */
$translateString = Mage::getModel('Magento_Core_Model_Resource_Translate_String');
$translateString->saveTranslate(
    'Fixture String', 'Fixture Db Translation', null, Magento_Core_Model_AppInterface::ADMIN_STORE_ID
);
