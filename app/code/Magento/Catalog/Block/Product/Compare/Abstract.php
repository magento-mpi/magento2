<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Compare Products Abstract Block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Catalog_Block_Product_Compare_Abstract extends Magento_Catalog_Block_Product_Abstract
{
    /**
     * Catalog product compare
     *
     * @var Magento_Catalog_Helper_Product_Compare
     */
    protected $_catalogProductCompare = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Catalog_Helper_Product_Compare $catalogProductCompare
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Catalog_Helper_Product_Compare $catalogProductCompare,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_catalogProductCompare = $catalogProductCompare;
        parent::__construct($eventManager, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Retrieve Product Compare Helper
     *
     * @return Magento_Catalog_Helper_Product_Compare
     */
    protected function _getHelper()
    {
        return $this->_catalogProductCompare;
    }

    /**
     * Retrieve Remove Item from Compare List URL
     *
     * @param Magento_Catalog_Model_Product $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->_getHelper()->getRemoveUrl($item);
    }
}
