<?php
/**
 * Webhook module helper needed for translation.
 *
 * As long as we have code like Magento_Backend_Model_Menu_Item that calls helpers for every module
 * we will need every module to have a Data helper, even if the module itself doesn't use it thanks to
 * DI being available for translation.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Webhook_Helper_Data extends Magento_Core_Helper_Abstract
{
}
