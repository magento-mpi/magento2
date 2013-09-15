<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Widget Instance edit container
 *
 * @category    Magento
 * @package     Magento_Widget
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Widget\Block\Adminhtml\Widget\Instance;

class Edit extends \Magento\Adminhtml\Block\Widget\Form\Container
{
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

    /**
     * Internal constructor
     *
     */
    protected function _construct()
    {
        $this->_objectId = 'instance_id';
        $this->_blockGroup = 'Magento_Widget';
        $this->_controller = 'adminhtml_widget_instance';
        parent::_construct();
    }

    /**
     * Getter
     *
     * @return \Magento\Widget\Model\Widget\Instance
     */
    public function getWidgetInstance()
    {
        return $this->_coreRegistry->registry('current_widget_instance');
    }

    /**
     * Prepare layout.
     * Adding save_and_continue button
     *
     * @return \Magento\Widget\Block\Adminhtml\Widget\Instance\Edit
     */
    protected function _preparelayout()
    {
        if ($this->getWidgetInstance()->isCompleteToCreate()) {
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
        } else {
            $this->removeButton('save');
        }
        return parent::_prepareLayout();
    }

    /**
     * Return translated header text depending on creating/editing action
     *
     * @return string
     */
    public function getHeaderText()
    {
        if ($this->getWidgetInstance()->getId()) {
            return __('Widget "%1"', $this->escapeHtml($this->getWidgetInstance()->getTitle()));
        } else {
            return __('New Widget Instance');
        }
    }

    /**
     * Return validation url for edit form
     *
     * @return string
     */
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    /**
     * Return save url for edit form
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true, 'back'=>null));
    }
}
