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
namespace Magento\Catalog\Block\Product\Compare;

abstract class AbstractCompare extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Catalog product compare
     *
     * @var Magento_Catalog_Helper_Product_Compare
     */
    protected $_catalogProductCompare = null;

    /**
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Catalog_Helper_Product_Compare $catalogProductCompare
     * @param Magento_Tax_Helper_Data $taxData
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Catalog_Helper_Product_Compare $catalogProductCompare,
        Magento_Tax_Helper_Data $taxData,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_catalogProductCompare = $catalogProductCompare;
        parent::__construct($coreRegistry, $taxData, $catalogData, $coreData, $context, $data);
    }

    /**
     * Retrieve Product Compare Helper
     *
     * @return \Magento\Catalog\Helper\Product\Compare
     */
    protected function _getHelper()
    {
        return $this->_catalogProductCompare;
    }

    /**
     * Retrieve Remove Item from Compare List URL
     *
     * @param \Magento\Catalog\Model\Product $item
     * @return string
     */
    public function getRemoveUrl($item)
    {
        return $this->_getHelper()->getRemoveUrl($item);
    }
}
