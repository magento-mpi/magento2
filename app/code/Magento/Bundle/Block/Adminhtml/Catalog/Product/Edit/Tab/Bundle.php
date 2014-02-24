<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab;

/**
 * Adminhtml catalog product bundle items tab block
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Bundle extends \Magento\Backend\Block\Widget
    implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * @var mixed
     */
    protected $_product = null;

    /**
     * @var string
     */
    protected $_template = 'product/edit/bundle.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('adminhtml/bundle_product_edit/form', array('_current' => true));
    }

    /**
     * @return string
     */
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
        $this->addChild('add_button', 'Magento\Backend\Block\Widget\Button', array(
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

    /**
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }

    /**
     * @return string
     */
    public function getOptionsBoxHtml()
    {
        return $this->getChildHtml('options_box');
    }

    /**
     * @return string
     */
    public function getFieldSuffix()
    {
        return 'product';
    }

    /**
     * @return mixed
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('product');
    }

    /**
     * @return string
     */
    public function getTabLabel()
    {
        return __('Bundle Items');
    }

    /**
     * @return string
     */
    public function getTabTitle()
    {
        return __('Bundle Items');
    }

    /**
     * @return bool
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * @return bool
     */
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
