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
class Magento_CatalogEvent_Block_Catalog_Product_Event extends Magento_CatalogEvent_Block_Event_Abstract
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
        return $this->_catalogEventData->isEnabled() &&
               $this->getProduct() &&
               $this->getEvent() &&
               $this->getEvent()->canDisplayProductPage() &&
               !$this->getProduct()->getEventNoTicker();
    }

}
