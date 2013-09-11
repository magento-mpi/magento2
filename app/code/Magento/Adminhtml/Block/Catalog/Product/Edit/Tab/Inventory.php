<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product inventory data
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Edit\Tab;

class Inventory extends \Magento\Adminhtml\Block\Widget
{
    protected $_template = 'catalog/product/tab/inventory.phtml';

    /**
     * @var \Magento\Core\Model\StoreManager
     */
    protected $_storeManager;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\StoreManager $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\StoreManager $storeManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_storeManager = $storeManager;
    }

    public function getBackordersOption()
    {
        if (\Mage::helper('Magento\Catalog\Helper\Data')->isModuleEnabled('Magento_CatalogInventory')) {
            return \Mage::getSingleton('Magento\CatalogInventory\Model\Source\Backorders')->toOptionArray();
        }

        return array();
    }

    /**
     * Retrieve stock option array
     *
     * @return array
     */
    public function getStockOption()
    {
        if (\Mage::helper('Magento\Catalog\Helper\Data')->isModuleEnabled('Magento_CatalogInventory')) {
            return \Mage::getSingleton('Magento\CatalogInventory\Model\Source\Stock')->toOptionArray();
        }

        return array();
    }

    /**
     * Return current product instance
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return \Mage::registry('product');
    }

    /**
     * Retrieve Catalog Inventory  Stock Item Model
     *
     * @return \Magento\CatalogInventory\Model\Stock\Item
     */
    public function getStockItem()
    {
        return $this->getProduct()->getStockItem();
    }

    public function isConfigurable()
    {
        return $this->getProduct()->isConfigurable();
    }

    public function getFieldValue($field)
    {
        if ($this->getStockItem()) {
            return $this->getStockItem()->getDataUsingMethod($field);
        }

        return \Mage::getStoreConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_ITEM . $field);
    }

    public function getConfigFieldValue($field)
    {
        if ($this->getStockItem()) {
            if ($this->getStockItem()->getData('use_config_' . $field) == 0) {
                return $this->getStockItem()->getData($field);
            }
        }

        return \Mage::getStoreConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_ITEM . $field);
    }

    public function getDefaultConfigValue($field)
    {
        return \Mage::getStoreConfig(\Magento\CatalogInventory\Model\Stock\Item::XML_PATH_ITEM . $field);
    }

    /**
     * Is readonly stock
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getInventoryReadonly();
    }

    public function isNew()
    {
        if ($this->getProduct()->getId()) {
            return false;
        }
        return true;
    }

    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * Check Whether product type can have fractional quantity or not
     *
     * @return bool
     */
    public function canUseQtyDecimals()
    {
        return $this->getProduct()->getTypeInstance()->canUseQtyDecimals();
    }

    /**
     * Check if product type is virtual
     *
     * @return boolean
     */
    public function isVirtual()
    {
        return $this->getProduct()->getIsVirtual();
    }

    /**
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
