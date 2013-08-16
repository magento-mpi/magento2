<?php
/**
 * Webhook module helper needed for translation.
 *
 * As long as we have code like Mage_Backend_Model_Menu_Item that calls Mage::helper() for every module
 * we will need every module to have a Data helper, even if the module itself doesn't use it thanks to
 * DI being available for translation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Webhook_Helper_Data extends Mage_Core_Helper_Abstract
{
}
