<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product after creation popup window
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Catalog_Product_Created extends Mage_Adminhtml_Block_Widget
{
    protected $_configurableProduct;
    protected $_product;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('catalog/product/created.phtml');
    }


    protected function _prepareLayout()
    {
        $this->setChild(
            'close_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'   => Mage::helper('Mage_Catalog_Helper_Data')->__('Close Window'),
                    'onclick' => 'addProduct(true)'
                ))
        );
    }


    public function getCloseButtonHtml()
    {
        return $this->getChildHtml('close_button');
    }

    public function getProductId()
    {
        return (int) $this->getRequest()->getParam('id');
    }

    /**
     * Indentifies edit mode of popup
     *
     * @return boolean
     */
    public function isEdit()
    {
        return (bool) $this->getRequest()->getParam('edit');
    }

    /**
     * Retrive serialized json with configurable attributes values of simple
     *
     * @return string
     */
    public function getAttributesJson()
    {
        $result = array();
        foreach ($this->getAttributes() as $attribute) {
            $value = $this->getProduct()->getAttributeText($attribute->getAttributeCode());

            $result[] = array(
                'label'         => $value,
                'value_index'   => $this->getProduct()->getData($attribute->getAttributeCode()),
                'attribute_id'  => $attribute->getId()
            );
        }

        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($result);
    }

    public function getAttributes()
    {
        if ($this->getConfigurableProduct()->getId()) {
            return $this->getConfigurableProduct()
                ->getTypeInstance()
                ->getUsedProductAttributes($this->getConfigurableProduct());
        }

        $attributes = array();

        $attributesIds = $this->getRequest()->getParam('required');
        if ($attributesIds) {
            $attributesIds = explode(',', $attributesIds);
            foreach ($attributesIds as $attributeId) {
                $attribute = $this->getProduct()
                    ->getTypeInstance()
                    ->getAttributeById($attributeId, $this->getProduct());
                if (!$attribute) {
                    continue;
                }
                $attributes[] = $attribute;
            }
        }

        return $attributes;
    }

    /**
     * Retrive configurable product for created/edited simple
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getConfigurableProduct()
    {
        if (is_null($this->_configurableProduct)) {
            $this->_configurableProduct = Mage::getModel('Mage_Catalog_Model_Product')
                ->setStore(0)
                ->load($this->getRequest()->getParam('product'));
        }
        return $this->_configurableProduct;
    }

    /**
     * Retrive product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        if (is_null($this->_product)) {
            $this->_product = Mage::getModel('Mage_Catalog_Model_Product')
                ->setStore(0)
                ->load($this->getRequest()->getParam('id'));
        }
        return $this->_product;
    }
} // Class Mage_Adminhtml_Block_Catalog_Product_Created End
