<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Contacts
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
class Mage_Contacts_Model_System_Config_Backend_Links extends Mage_Backend_Model_Config_Backend_Cache
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(Magento_Core_Model_Store::CACHE_TAG, Magento_Cms_Model_Block::CACHE_TAG);

}
