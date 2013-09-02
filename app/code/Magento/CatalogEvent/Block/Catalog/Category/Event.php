<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogEvent
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Catalog Event on category page
 *
 * @category   Magento
 * @package    Magento_CatalogEvent
 */
class Magento_CatalogEvent_Block_Catalog_Category_Event extends Magento_CatalogEvent_Block_Event_Abstract
{
    /**
     * Catalog event data
     *
     * @var Magento_CatalogEvent_Helper_Data
     */
    protected $_catalogEventData = null;

    /**
     * @param Magento_CatalogEvent_Helper_Data $catalogEventData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_CatalogEvent_Helper_Data $catalogEventData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_catalogEventData = $catalogEventData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return current category event
     *
     * @return Magento_CategoryEvent_Model_Event
     */
    public function getEvent()
    {
        return $this->getCategory()->getEvent();
    }

    /**
     * Return current category
     *
     * @return Magento_Catalog_Model_Category
     */
    public function getCategory()
    {
        return Mage::registry('current_category');
    }

    /**
     * Return category url
     *
     * @param Magento_Data_Tree_Node $category
     * @return string
     */
    public function getCategoryUrl($category = null)
    {
        if ($category === null) {
            $category = $this->getCategory();
        }

        return $category->getUrl();
    }

    /**
     * Check availability to display event block
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return $this->_catalogEventData->isEnabled() &&
               $this->getEvent() &&
               $this->getEvent()->canDisplayCategoryPage();
    }
}
