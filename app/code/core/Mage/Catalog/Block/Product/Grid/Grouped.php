<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Products in grouped grid
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Grid_Grouped extends Mage_Backend_Block_Widget_Grid
{
    /**
     * Input name product data will be serialized into
     */
    protected $_hiddenInputName;

    /**
     * Names of the inputs to serialize
     */
    protected $_fieldsToSave = array();

    protected function _construct()
    {
        parent::_construct();
        $this->setId('super_product_grid');
        $this->setDefaultSort('entity_id');
        $this->setSkipGenerateContent(true);
        $this->setUseAjax(true);
    }

    /**
     * Retrieve grouped products
     *
     * @return array
     */
    public function getAssociatedProducts()
    {
        $associatedProducts = Mage::registry('current_product')->getTypeInstance()
            ->getAssociatedProducts(Mage::registry('current_product'));
        $products = array();
        foreach ($associatedProducts as $product) {
            $products[$product->getId()] = array(
                'qty'       => $product->getQty(),
                'position'  => $product->getPosition()
            );
        }
        return $this->helper('Mage_Core_Helper_Data')->jsonEncode($products);
    }

    /**
     * Get associated product ids
     *
     * @return array
     */
    public function getAssociatedProductsIds()
    {
        $associatedProducts = Mage::registry('current_product')->getTypeInstance()
            ->getAssociatedProducts(Mage::registry('current_product'));
        $ids = array();
        foreach ($associatedProducts as $product) {
            $ids[] = $product->getId();
        }
        return $this->helper('Mage_Core_Helper_Data')->jsonEncode($ids);
    }

    /**
     * Get hidden input name
     *
     * @return string
     */
    public function getHiddenInputName()
    {
        return $this->_hiddenInputName;
    }

    /**
     * Get fields names
     *
     * @return array
     */
    public function getFieldsToSave()
    {
        return $this->_fieldsToSave;
    }

    /**
     * Init function
     *
     * @param string $hiddenInputName
     * @param array $fieldsToSave
     */
    public function setGridData($hiddenInputName, $fieldsToSave = array())
    {
        $this->_hiddenInputName = $hiddenInputName;
        $this->_fieldsToSave = $fieldsToSave;
    }
}
