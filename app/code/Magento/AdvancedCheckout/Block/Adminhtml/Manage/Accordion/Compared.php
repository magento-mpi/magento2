<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion grid for products in compared list
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Compared
    extends Magento_AdvancedCheckout_Block_Adminhtml_Manage_Accordion_Abstract
{
    /**
     * Javascript list type name for this grid
     */
    protected $_listType = 'compared';

    /**
     * Adminhtml sales
     *
     * @var Magento_Adminhtml_Helper_Sales
     */
    protected $_adminhtmlSales = null;

    /**
     * @param Magento_Adminhtml_Helper_Sales $adminhtmlSales
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param array $data
     */
    public function __construct(
        Magento_Adminhtml_Helper_Sales $adminhtmlSales,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        array $data = array()
    ) {
        $this->_adminhtmlSales = $adminhtmlSales;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('source_compared');
        if ($this->_getStore()) {
            $this->setHeaderText(
                __('Products in the Comparison List (%1)', $this->getItemsCount())
            );
        }
    }


    /**
     * Return items collection
     *
     * @return Magento_Core_Model_Resource_Db_Collection_Abstract
     */
    public function getItemsCollection()
    {
        if (!$this->hasData('items_collection')) {
            $attributes = Mage::getSingleton('Magento_Catalog_Model_Config')->getProductAttributes();
            $collection = Mage::getModel('Magento_Catalog_Model_Product_Compare_List')
                ->getItemCollection()
                ->useProductItem(true)
                ->setStoreId($this->_getStore()->getId())
                ->addStoreFilter($this->_getStore()->getId())
                ->setCustomerId($this->_getCustomer()->getId())
                ->addAttributeToSelect($attributes)
                ->addAttributeToFilter('status', Magento_Catalog_Model_Product_Status::STATUS_ENABLED);
            Mage::getSingleton('Magento_CatalogInventory_Model_Stock_Status')->addIsInStockFilterToCollection($collection);
            $collection = $this->_adminhtmlSales->applySalableProductTypesFilter($collection);
            $collection->addOptionsToResult();
            $this->setData('items_collection', $collection);
        }
        return $this->_getData('items_collection');
    }

    /**
     * Return grid URL for sorting and filtering
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/viewCompared', array('_current'=>true));
    }
}
