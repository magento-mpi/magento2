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
 * Quantity and Stock Status attribute processing
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Product_Attribute_Backend_Stock extends Magento_Eav_Model_Entity_Attribute_Backend_Abstract
{
    /**
     * Stock item factory
     *
     * @var Magento_CatalogInventory_Model_Stock_ItemFactory
     */
    protected $_stockItemFactory;

    /**
     * Construct
     *
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_CatalogInventory_Model_Stock_ItemFactory $stockItemFactory
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_CatalogInventory_Model_Stock_ItemFactory $stockItemFactory
    ) {
        $this->_stockItemFactory = $stockItemFactory;
        parent::__construct($logger);
    }

    /**
     * Set inventory data to custom attribute
     *
     * @param Magento_Object $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Abstract
     */
    public function afterLoad($object)
    {
        $item = $this->_stockItemFactory->create();
        $item->loadByProduct($object);
        $object->setData(
            $this->getAttribute()->getAttributeCode(),
            array(
                'is_in_stock' => $item->getIsInStock(),
                'qty' => $item->getQty(),
            )
        );
        return parent::afterLoad($object);
    }

    /**
     * Prepare inventory data from custom attribute
     *
     * @param Magento_Catalog_Model_Product $object
     * @return Magento_Eav_Model_Entity_Attribute_Backend_Abstract|void
     */
    public function beforeSave($object)
    {
        $stockData = $object->getData($this->getAttribute()->getAttributeCode());
        if (isset($stockData['qty']) && $stockData['qty'] === '') {
            $stockData['qty'] = null;
        }
        if ($object->getStockData() !== null || $stockData !== null) {
            $object->setStockData(array_replace((array)$object->getStockData(), (array)$stockData));
        }
        $object->unsetData($this->getAttribute()->getAttributeCode());
        parent::beforeSave($object);
    }

    /**
     * Validate
     *
     * @param Magento_Catalog_Model_Product $object
     * @throws Magento_Core_Exception
     * @return bool
     */
    public function validate($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $value = $object->getData($attrCode);
        if (!empty($value['qty']) && !preg_match('/^-?\d*(\.|,)?\d{0,4}$/i', $value['qty'])) {
            throw new Magento_Core_Exception(
                __('Please enter a valid number in this field.')
            );
        }
        return true;
    }
}
