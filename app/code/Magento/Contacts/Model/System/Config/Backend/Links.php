<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Contacts
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Cache cleaner backend model
 *
 */
class Magento_Contacts_Model_System_Config_Backend_Links extends Magento_Backend_Model_Config_Backend_Cache
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(Magento_Core_Model_Store::CACHE_TAG, Magento_Cms_Model_Block::CACHE_TAG);

}
