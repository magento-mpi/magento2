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
 * Product in category grid
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Block_Product_Tab_Grouped extends Mage_Backend_Block_Widget_Grid
{

    protected $_hiddenInputName;
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
    public function getSelectedGroupedProducts()
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
        return $products;
    }

    /**
     * Get associated product ids
     *
     * @return array
     */
    public function getAssociatedProductsId()
    {
        $associatedProducts = Mage::registry('current_product')->getTypeInstance()
            ->getAssociatedProducts(Mage::registry('current_product'));
        $ids = array();
        foreach ($associatedProducts as $product) {
            $ids[] = $product->getId();
        }
        return $this->helper('Mage_Core_Helper_Data')->jsonEncode($ids);
    }

    public function getHiddenInputName()
    {
        return $this->_hiddenInputName;
    }

    public function getFieldsToSave()
    {
        return $this->_fieldsToSave;
    }

    public function setGridData($hiddenInputName, $fieldsToSave = array())
    {
        $this->_hiddenInputName = $hiddenInputName;
        $this->_fieldsToSave = $fieldsToSave;
    }
}
