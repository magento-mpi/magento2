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
class Enterprise_CatalogEvent_Block_Catalog_Product_Event extends Enterprise_CatalogEvent_Block_Event_Abstract
{
    /**
     * Return current category event
     *
     * @return Enterprise_CategoryEvent_Model_Event
     */
    public function getEvent()
    {
        if ($this->getProduct()) {
            return $this->getProduct()->getEvent();
        }

        return false;
    }

    /**
     * Return current category
     *
     * @return Magento_Catalog_Model_Category
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Check availability to display event block
     *
     * @return boolean
     */
    public function canDisplay()
    {
        return Mage::helper('Enterprise_CatalogEvent_Helper_Data')->isEnabled() &&
               $this->getProduct() &&
               $this->getEvent() &&
               $this->getEvent()->canDisplayProductPage() &&
               !$this->getProduct()->getEventNoTicker();
    }

}
