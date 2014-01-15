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
 * Customer edit block
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Adminhtml\Product;

class Edit extends \Magento\Backend\Block\Widget
{
    protected $_template = 'catalog/product/edit.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Eav\Model\Entity\Attribute\SetFactory
     */
    protected $_attributeSetFactory;

    /**
     * @var \Magento\Json\EncoderInterface
     */
    protected $jsonEncoder;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $_productHelper;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Eav\Model\Entity\Attribute\SetFactory $attributeSetFactory,
        \Magento\Core\Model\Registry $registry,
        \Magento\Catalog\Helper\Product $productHelper,
        array $data = array()
    ) {
        $this->_productHelper = $productHelper;
        $this->_attributeSetFactory = $attributeSetFactory;
        $this->_coreRegistry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();
        $this->setId('product_edit');
        $this->setUseContainer(true);
    }

    /**
     * Retrieve currently edited product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }

    /**
     * Add elements in layout
     *
     * @return \Magento\Catalog\Block\Adminhtml\Product\Edit
     */
    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->addChild('back_button', 'Magento\Backend\Block\Widget\Button', array(
                'label' => __('Back'),
                'title' => __('Back'),
                'onclick' => 'setLocation(\''
                    . $this->getUrl('catalog/*/', array('store' => $this->getRequest()->getParam('store', 0))) . '\')',
                'class' => 'action-back'
            ));
        } else {
            $this->addChild('back_button', 'Magento\Backend\Block\Widget\Button', array(
                'label' => __('Close Window'),
                'onclick' => 'window.close()',
                'class' => 'cancel'
            ));
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->addChild('reset_button', 'Magento\Backend\Block\Widget\Button', array(
                'label' => __('Reset'),
                'onclick' => 'setLocation(\'' . $this->getUrl('catalog/*/*', array('_current' => true)) . '\')'
            ));
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->addChild('save-split-button', 'Magento\Backend\Block\Widget\Button\SplitButton', array(
                'id' => 'save-split-button',
                'label' => __('Save'),
                'class_name' => 'Magento\Backend\Block\Widget\Button\SplitButton',
                'button_class' => 'widget-button-save',
                'options' => $this->_getSaveSplitButtonOptions()
            ));
        }

        return parent::_prepareLayout();
    }

    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }

    public function getSaveButtonHtml()
    {
        return $this->getChildHtml('save_button');
    }

    public function getSaveAndEditButtonHtml()
    {
        return $this->getChildHtml('save_and_edit_button');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    public function getDuplicateButtonHtml()
    {
        return $this->getChildHtml('duplicate_button');
    }

    /**
     * Get Save Split Button html
     *
     * @return string
     */
    public function getSaveSplitButtonHtml()
    {
        return $this->getChildHtml('save-split-button');
    }

    public function getValidationUrl()
    {
        return $this->getUrl('catalog/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('catalog/*/save', array('_current'=>true, 'back'=>null));
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('catalog/*/save', array(
            '_current'   => true,
            'back'       => 'edit',
            'tab'        => '{{tab_id}}',
            'active_tab' => null
        ));
    }

    public function getProductId()
    {
        return $this->getProduct()->getId();
    }

    public function getProductSetId()
    {
        $setId = false;
        if (!($setId = $this->getProduct()->getAttributeSetId()) && $this->getRequest()) {
            $setId = $this->getRequest()->getParam('set', null);
        }
        return $setId;
    }

    public function getIsGrouped()
    {
        return $this->getProduct()->isGrouped();
    }

    public function getDuplicateUrl()
    {
        return $this->getUrl('catalog/*/duplicate', array('_current'=>true));
    }

    public function getHeader()
    {
        if ($this->getProduct()->getId()) {
            $header = $this->escapeHtml($this->getProduct()->getName());
        } else {
            $header = __('New Product');
        }
        return $header;
    }

    public function getAttributeSetName()
    {
        if ($setId = $this->getProduct()->getAttributeSetId()) {
            $set = $this->_attributeSetFactory->create()->load($setId);
            return $set->getAttributeSetName();
        }
        return '';
    }

    public function getIsConfigured()
    {
        $result = true;

        $product = $this->getProduct();
        if ($product->isConfigurable()) {
            $superAttributes = $product->getTypeInstance()->getUsedProductAttributeIds($product);
            $result = !empty($superAttributes);
        }

        return $result;
    }

    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }

    /**
     * Get fields masks from config
     *
     * @return array
     */
    public function getFieldsAutogenerationMasks()
    {
        return $this->_productHelper->getFieldsAutogenerationMasks();
    }

    /**
     * Retrieve available placeholders
     *
     * @return array
     */
    public function getAttributesAllowedForAutogeneration()
    {
        return $this->_productHelper->getAttributesAllowedForAutogeneration();
    }

    /**
     * Get data for JS (product type transition)
     *
     * @return string
     */
    public function getTypeSwitcherData()
    {
        return $this->jsonEncoder->encode(array(
            'tab_id' => 'product_info_tabs_downloadable_items',
            'is_virtual_id' => \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Weight::VIRTUAL_FIELD_HTML_ID,
            'weight_id' => 'weight',
            'current_type' => $this->getProduct()->getTypeId(),
            'attributes' => $this->_getAttributes(),
        ));
    }

    /**
     * Get formed array with attribute codes and Apply To property
     *
     * @return array
     */
    protected function _getAttributes()
    {
        /** @var $product \Magento\Catalog\Model\Product */
        $product = $this->getProduct();
        $attributes = array();

        foreach ($product->getAttributes() as $key => $attribute) {
            $attributes[$key] = $attribute->getApplyTo();
        }
        return $attributes;
    }

    /**
     * Get dropdown options for save split button
     *
     * @return array
     */
    protected function _getSaveSplitButtonOptions()
    {
        $options = array();
        if (!$this->getRequest()->getParam('popup')) {
            $options[] = array(
                'id' => 'edit-button',
                'label' => __('Save & Edit'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '[data-form=edit-product]'),
                    ),
                ),
                'default' => true,
            );
        }

        $options[] = array(
            'id' => 'new-button',
            'label' => __('Save & New'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndNew', 'target' => '[data-form=edit-product]'),
                ),
            ),
        );
        if (!$this->getRequest()->getParam('popup') && $this->getProduct()->isDuplicable()) {
            $options[] = array(
                'id' => 'duplicate-button',
                'label' => __('Save & Duplicate'),
                'data_attribute' => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndDuplicate', 'target' => '[data-form=edit-product]'),
                    ),
                ),
            );
        }
        $options[] = array(
            'id' => 'close-button',
            'label' => __('Save & Close'),
            'data_attribute' => array(
                'mage-init' => array(
                    'button' => array('event' => 'save', 'target' => '[data-form=edit-product]'),
                ),
            ),
        );
        return $options;
    }

    /**
     * Check whether new product is being created
     *
     * @return bool
     */
    protected function _isProductNew()
    {
        $product = $this->getProduct();
        return !$product || !$product->getId();
    }
}
