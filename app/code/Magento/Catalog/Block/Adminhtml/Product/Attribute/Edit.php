<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Attribute;

/**
 * Product attribute edit page
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Block group name
     *
     * @var string
     */
    protected $_blockGroup = 'Magento_Catalog';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Widget\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'attribute_id';
        $this->_controller = 'adminhtml_product_attribute';

        parent::_construct();

        if ($this->getRequest()->getParam('popup')) {
            $this->buttonList->remove('back');
            if ($this->getRequest()->getParam('product_tab') != 'variations') {
                $this->addButton(
                    'save_in_new_set',
                    array(
                        'label' => __('Save in New Attribute Set'),
                        'class' => 'save',
                        'onclick' => 'saveAttributeInNewSet(\'' . __('Enter Name for New Attribute Set') . '\')'
                    ),
                    100
                );
            }
        } else {
            $this->addButton(
                'save_and_edit_button',
                array(
                    'label' => __('Save and Continue Edit'),
                    'class' => 'save',
                    'data_attribute' => array(
                        'mage-init' => array(
                            'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form')
                        )
                    )
                )
            );
        }

        $this->buttonList->update('save', 'label', __('Save Attribute'));
        $this->buttonList->update('save', 'class', 'save primary');
        $this->buttonList->update(
            'save',
            'data_attribute',
            array('mage-init' => array('button' => array('event' => 'save', 'target' => '#edit_form')))
        );

        $entityAttribute = $this->_coreRegistry->registry('entity_attribute');
        if (!$entityAttribute || !$entityAttribute->getIsUserDefined()) {
            $this->buttonList->remove('delete');
        } else {
            $this->buttonList->update('delete', 'label', __('Delete Attribute'));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function addButton($buttonId, $data, $level = 0, $sortOrder = 0, $region = 'toolbar')
    {
        if ($this->getRequest()->getParam('popup')) {
            $region = 'header';
        }
        parent::addButton($buttonId, $data, $level, $sortOrder, $region);
    }

    /**
     * Retrieve header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_coreRegistry->registry('entity_attribute')->getId()) {
            $frontendLabel = $this->_coreRegistry->registry('entity_attribute')->getFrontendLabel();
            if (is_array($frontendLabel)) {
                $frontendLabel = $frontendLabel[0];
            }
            return __('Edit Product Attribute "%1"', $this->escapeHtml($frontendLabel));
        }
        return __('New Product Attribute');
    }

    /**
     * Retrieve URL for validation
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('catalog/*/validate', array('_current' => true));
    }

    /**
     * Retrieve URL for save
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl(
            'catalog/product_attribute/save',
            array('_current' => true, 'back' => null, 'product_tab' => $this->getRequest()->getParam('product_tab'))
        );
    }
}
