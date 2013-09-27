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
 * Catalog manage products block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product extends Magento_Adminhtml_Block_Widget_Container
{
    protected $_template = 'catalog/product.phtml';


    protected $_typeFactory;

    protected $_productFactory;

    /**
     * @param Magento_Catalog_Model_Product_TypeFactory $typeFactory
     * @param Magento_Catalog_Model_ProductFactory $productFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Model_Product_TypeFactory $typeFactory,
        Magento_Catalog_Model_ProductFactory $productFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_productFactory = $productFactory;

        parent::__construct($coreData, $context, $data);
    }

    /**
     * Prepare button and grid
     *
     * @return Magento_Adminhtml_Block_Catalog_Product
     */
    protected function _prepareLayout()
    {
        $addButtonProps = array(
            'id' => 'add_new_product',
            'label' => __('Add Product'),
            'class' => 'btn-add',
            'button_class' => 'btn-round',
            'class_name' => 'Magento_Backend_Block_Widget_Button_Split',
            'options' => $this->_getAddProductButtonOptions(),
        );
        $this->_addButton('add_new', $addButtonProps);

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Magento_Adminhtml_Block_Catalog_Product_Grid', 'product.grid')
        );
        return parent::_prepareLayout();
    }

    /**
     * Retrieve options for 'Add Product' split button
     *
     * @return array
     */
    protected function _getAddProductButtonOptions()
    {
        $splitButtonOptions = array();

        foreach ($this->_typeFactory->create()->getOptionArray() as $key => $label) {
            $splitButtonOptions[$key] = array(
                'label'     => $label,
                'onclick'   => "setLocation('" . $this->_getProductCreateUrl($key) . "')",
                'default'   => Magento_Catalog_Model_Product_Type::DEFAULT_TYPE == $key
            );
        }

        return $splitButtonOptions;
    }

    /**
     * Retrieve product create url by specified product type
     *
     * @param string $type
     * @return string
     */
    protected function _getProductCreateUrl($type)
    {
        return $this->getUrl('*/*/new', array(
            'set'   => $this->_productFactory->create()->getDefaultAttributeSetId(),
            'type'  => $type
        ));
    }

    /**
     * Render grid
     *
     * @return string
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return $this->_storeManager->isSingleStoreMode();
    }
}
