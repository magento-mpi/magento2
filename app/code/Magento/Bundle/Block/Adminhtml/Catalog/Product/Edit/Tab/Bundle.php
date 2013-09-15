<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml catalog product bundle items tab block
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab;

class Bundle extends \Magento\Adminhtml\Block\Widget
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    protected $_product = null;

    protected $_template = 'product/edit/bundle.phtml';

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    public function getTabUrl()
    {
        return $this->getUrl('*/bundle_product_edit/form', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Prepare layout
     *
     * @return \Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle
     */
    protected function _prepareLayout()
    {
        $this->addChild('add_button', 'Magento\Adminhtml\Block\Widget\Button', array(
            'label' => __('Create New Option'),
            'class' => 'add',
            'id'    => 'add_new_option',
            'on_click' => 'bOption.add()'
        ));

        $this->setChild('options_box',
            $this->getLayout()->createBlock('Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option',
                'adminhtml.catalog.product.edit.tab.bundle.option')
        );

        return parent::_prepareLayout();
    }

    /**
     * Check block readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getCompositeReadonly();
    }

    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }

    public function getFieldSuffix()
    {
        return 'product';
    }

    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    public function getTabLabel()
    {
        return __('Bundle Items');
    }

    public function getTabTitle()
    {
        return __('Bundle Items');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }

    /**
     * Get parent tab code
     *
     * @return string
     */
    public function getParentTab()
    {
        return 'product-details';
    }
}
