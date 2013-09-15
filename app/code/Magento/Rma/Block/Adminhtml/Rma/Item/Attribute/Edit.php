<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Item Attributes Edit container
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Item\Attribute;

class Edit
    extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return current customer address attribute instance
     *
     * @return \Magento\Rma\Model\Item\Attribute
     */
    protected function _getAttribute()
    {
        return $this->_coreRegistry->registry('entity_attribute');
    }

    /**
     * Initialize Customer Address Attribute Edit Container
     *
     */
    protected function _construct()
    {
        $this->_objectId    = 'attribute_id';
        $this->_blockGroup  = 'Magento_Rma';
        $this->_controller  = 'adminhtml_rma_item_attribute';

        parent::_construct();

        $this->_addButton(
            'save_and_edit_button',
            array(
                'label'     => __('Save and Continue Edit'),
                'class'     => 'save',
                'data_attribute'  => array(
                    'mage-init' => array(
                        'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                    ),
                ),
            ),
            100
        );

        $this->_updateButton('save', 'label', __('Save Attribute'));

        if (!$this->_getAttribute() || !$this->_getAttribute()->getIsUserDefined()) {
            $this->_removeButton('delete');
        } else {
            $this->_updateButton('delete', 'label', __('Delete Attribute'));
        }
    }

    /**
     * Return header text for edit block
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->_getAttribute()->getId()) {
            $label = $this->_getAttribute()->getFrontendLabel();
            if (is_array($label)) {
                // restored label
                $label = $label[0];
            }
            return __('Edit Return Item Attribute "%1"', $label);
        } else {
            return __('New Return Item Attribute');
        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current' => true));
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'back' => null));
    }
}
