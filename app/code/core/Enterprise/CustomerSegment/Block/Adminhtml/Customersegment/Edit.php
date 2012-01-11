<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit form for customer segment configuration
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Initialize form
     */
    public function __construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Enterprise_CustomerSegment';

        parent::__construct();
        /** @var Enterprise_CustomerSegment_Model_Segment */
        $segment = Mage::registry('current_customer_segment');
        if ($segment) {
            if ($segment->getId()
                && $segment->getApplyTo() != Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS
            ) {
                $this->_addButton('match_customers', array(
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Refresh Segment Data'),
                    'onclick' => 'setLocation(\'' . $this->getMatchUrl() . '\')',
                ), -1);
            }

            if ($segment->isReadonly()) {
                $this->_removeButton('save');
                $this->_removeButton('delete');
            } else {
                $this->_updateButton('save', 'label', Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Save'));
                $this->_updateButton('delete', 'label', Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Delete'));
                $this->_addButton('save_and_continue_edit', array(
                    'class' => 'save',
                    'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Save and Continue Edit'),
                    'onclick' => 'saveAndContinueEdit()',
                ), 3);

                $this->_formScripts[] = "
                    function saveAndContinueEdit() {
                        editForm.submit($('edit_form').action + 'back/edit/');
                    }";
            }
        }
    }

    /**
     * Get url for run segment customers matching
     *
     * @return string
     */
    public function getMatchUrl()
    {
        $segment = Mage::registry('current_customer_segment');
        return $this->getUrl('*/*/match', array('id'=>$segment->getId()));
    }

    /**
     * Return form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $segment = Mage::registry('current_customer_segment');
        if ($segment->getSegmentId()) {
            return Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__("Edit Segment '%s'", $this->escapeHtml($segment->getName()));
        }
        else {
            return Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('New Segment');
        }
    }
}
