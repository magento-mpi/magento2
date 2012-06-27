<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog event backend config cache model
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Model_Adminhtml_System_Config_Backend_Cache
    extends Mage_Adminhtml_Model_System_Config_Backend_Cache
{
    /**
     * Cache tags to clean
     *
     * @var array
     */
    protected $_cacheTags = array(
        Mage_Backend_Block_Menu::CACHE_TAGS
    );
}
