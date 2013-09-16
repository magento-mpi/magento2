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
namespace Magento\Adminhtml\Block\Catalog;

class Product extends \Magento\Adminhtml\Block\Widget\Container
{
    protected $_template = 'catalog/product.phtml';

    /**
     * Prepare button and grid
     *
     * @return \Magento\Adminhtml\Block\Catalog\Product
     */
    protected function _prepareLayout()
    {
        $addButtonProps = array(
            'id' => 'add_new_product',
            'label' => __('Add Product'),
            'class' => 'btn-add',
            'button_class' => 'btn-round',
            'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
            'options' => $this->_getAddProductButtonOptions(),
        );
        $this->_addButton('add_new', $addButtonProps);

        $this->setChild(
            'grid',
            $this->getLayout()->createBlock('Magento\Adminhtml\Block\Catalog\Product\Grid', 'product.grid')
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

        foreach (\Mage::getModel('Magento\Catalog\Model\Product\Type')->getOptionArray() as $key => $label) {
            $splitButtonOptions[$key] = array(
                'label'     => $label,
                'onclick'   => "setLocation('" . $this->_getProductCreateUrl($key) . "')",
                'default'   => \Magento\Catalog\Model\Product\Type::DEFAULT_TYPE == $key
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
            'set'   => \Mage::getModel('Magento\Catalog\Model\Product')->getDefaultAttributeSetId(),
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
        return \Mage::app()->isSingleStoreMode();
    }
}
