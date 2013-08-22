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
 * Catalog Event on category page
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogEvent
 */
class Enterprise_CatalogEvent_Block_Catalog_Category_Event extends Enterprise_CatalogEvent_Block_Event_Abstract
{
    /**
     * Return current category event
     *
     * @return Enterprise_CategoryEvent_Model_Event
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
        return Mage::helper('Enterprise_CatalogEvent_Helper_Data')->isEnabled() &&
               $this->getEvent() &&
               $this->getEvent()->canDisplayCategoryPage();
    }
}
