<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var Mage_Core_Model_Resource_Translate_String $translateString */
$translateString = Mage::getModel('Mage_Core_Model_Resource_Translate_String');
$translateString->saveTranslate('Fixture String', 'Fixture Db Translation');
