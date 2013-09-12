<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Ajax_Serializer extends Magento_Core_Block_Template
{
    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('catalog/product/edit/serializer.phtml');
        return $this;
    }

    public function getProductsJSON()
    {
        $result = array();
        if ($this->getProducts()) {
            $isEntityId = $this->getIsEntityId();
            foreach ($this->getProducts() as $product) {
                $id = $isEntityId ? $product->getEntityId() : $product->getId();
                $result[$id] = $product->toArray(array('qty', 'position'));
            }
        }
        return $result ? Zend_Json::encode($result) : '{}';
    }

    /**
     * Initialize grid block under the "Related Products", "Up-sells", "Cross-sells" sections
     *
     * @param string $blockName
     * @param string $getProductFunction
     * @param string $inputName
     */
    public function initSerializerBlock($blockName, $getProductFunction, $inputName)
    {
        if ($block = $this->getLayout()->getBlock($blockName)) {
            $this->setGridBlock($block)
                ->setProducts($this->_coreRegistry->registry('current_product')->$getProductFunction())
                ->setInputElementName($inputName);
        }
    }
}
