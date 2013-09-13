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
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product;

class Edit extends \Magento\Adminhtml\Block\Widget
{
    protected $_template = 'catalog/product/edit.phtml';

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
        return \Mage::registry('current_product');
    }

    /**
     * Add elements in layout
     *
     * @return \Magento\Adminhtml\Block\Catalog\Product\Edit
     */
    protected function _prepareLayout()
    {
        if (!$this->getRequest()->getParam('popup')) {
            $this->addChild('back_button', 'Magento\Adminhtml\Block\Widget\Button', array(
                'label' => __('Back'),
                'title' => __('Back'),
                'onclick' => 'setLocation(\''
                    . $this->getUrl('*/*/', array('store' => $this->getRequest()->getParam('store', 0))) . '\')',
                'class' => 'action-back'
            ));
        } else {
            $this->addChild('back_button', 'Magento\Adminhtml\Block\Widget\Button', array(
                'label' => __('Close Window'),
                'onclick' => 'window.close()',
                'class' => 'cancel'
            ));
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->addChild('reset_button', 'Magento\Adminhtml\Block\Widget\Button', array(
                'label' => __('Reset'),
                'onclick' => 'setLocation(\'' . $this->getUrl('*/*/*', array('_current' => true)) . '\')'
            ));
        }

        if (!$this->getProduct()->isReadonly()) {
            $this->addChild('save-split-button', 'Magento\Backend\Block\Widget\Button\Split', array(
                'id' => 'save-split-button',
                'label' => __('Save'),
                'class_name' => '\Magento\Backend\Block\Widget\Button\Split',
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
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }

    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array(
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
        return $this->getUrl('*/*/duplicate', array('_current'=>true));
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
            $set = \Mage::getModel('Magento\Eav\Model\Entity\Attribute\Set')
                ->load($setId);
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
        return $this->helper('Magento\Catalog\Helper\Product')->getFieldsAutogenerationMasks();
    }

    /**
     * Retrieve available placeholders
     *
     * @return array
     */
    public function getAttributesAllowedForAutogeneration()
    {
        return $this->helper('Magento\Catalog\Helper\Product')->getAttributesAllowedForAutogeneration();
    }

    /**
     * Get data for JS (product type transition)
     *
     * @return string
     */
    public function getTypeSwitcherData()
    {
        return \Mage::helper('Magento\Core\Helper\Data')->jsonEncode(array(
            'tab_id' => 'product_info_tabs_downloadable_items',
            'is_virtual_id' => \Magento\Adminhtml\Block\Catalog\Product\Helper\Form\Weight::VIRTUAL_FIELD_HTML_ID,
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
