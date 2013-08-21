<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin tag edit block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Magento_Tag_Block_Adminhtml_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Add and update buttons
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId   = 'tag_id';
        $this->_controller = 'adminhtml';
        $this->_blockGroup = 'Magento_Tag';

        parent::_construct();

        $this->_updateButton('save', 'label', __('Save Tag'));
        $this->_updateButton('delete', 'label', __('Delete Tag'));

        if (!Mage::registry('current_tag')) {
            return;
        }

        $this->addButton('save_and_edit_button', array(
            'label'   => __('Save and Continue Edit'),
            'class'   => 'save',
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array(
                        'event' => 'save',
                        'target' => '#edit_form',
                        'eventData' => array(
                            'action' => array(
                                'args' => array('ret' => 'edit', 'continue' => 'index'),
                            ),
                        ),
                    ),
                ),
            ),
        ), 1);
    }

    /**
     * Add child HTML to layout
     *
     * @return Magento_Tag_Block_Adminhtml_Edit
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->setChild(
                'store_switcher',
                $this->getLayout()->createBlock('Magento_Tag_Block_Adminhtml_Store_Switcher'))
             ->setChild(
                'tag_assign_accordion',
                $this->getLayout()->createBlock('Magento_Tag_Block_Adminhtml_Edit_Assigned'))
             ->setChild(
                'accordion',
                $this->getLayout()->createBlock('Magento_Tag_Block_Adminhtml_Edit_Accordion'));

        return $this;
    }

    /**
     * Retrieve Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('current_tag')->getId()) {
            return __("Edit Tag '%1'", $this->escapeHtml(Mage::registry('current_tag')->getName()));
        }
        return __('New Tag');
    }

    /**
     * Retrieve Accordions HTML
     *
     * @return string
     */
    public function getAcordionsHtml()
    {
        return $this->getChildHtml('accordion');
    }

    /**
     * Retrieve Tag Delete URL
     *
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('tag_id' => $this->getRequest()->getParam($this->_objectId), 'ret' => $this->getRequest()->getParam('ret', 'index')));
    }

    /**
     * Retrieve Assigned Tags Accordion HTML
     *
     * @return string
     */
    public function getTagAssignAccordionHtml()
    {
        return $this->getChildHtml('tag_assign_accordion');
    }

    /**
     * Retrieve Store Switcher HTML
     *
     * @return string
     */
    public function getStoreSwitcherHtml()
    {
        return $this->getChildHtml('store_switcher');
    }

    /**
     * Check whether it is single store mode
     *
     * @return bool
     */
    public function isSingleStoreMode()
    {
        return Mage::app()->isSingleStoreMode();
    }

    /**
     * Retrieve Tag Save URL
     *
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('_current'=>true));
    }

    /**
     * Retrieve Tag SaveAndContinue URL
     *
     * @return string
     */
    public function getSaveAndContinueUrl()
    {
        return $this->getUrl('*/*/save', array('_current' => true, 'ret' => 'edit', 'continue' => $this->getRequest()->getParam('ret', 'index'), 'store' => Mage::registry('current_tag')->getStoreId()));
    }

    /**
     * Get URL for back (reset) button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/' . $this->getRequest()->getParam('ret', 'index'));
    }
}
