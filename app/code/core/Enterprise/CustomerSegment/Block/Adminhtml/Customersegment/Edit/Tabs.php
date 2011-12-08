<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    /**
     * Intialize form
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('enterprise_customersegment_segment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Segment Information'));
    }

    /**
     * Add tab sections
     *
     * @return Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $this->addTab('general_section', array(
            'label'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('General Properties'),
            'title'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('General Properties'),
            'content'   => $this->getLayout()->createBlock(
                'Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_General'
            )->toHtml(),
            'active'    => true
        ));

        $this->addTab('conditions_section', array(
            'label'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions'),
            'title'     => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Conditions'),
            'content'   => $this->getLayout()->createBlock(
                'Enterprise_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions'
            )->toHtml(),
        ));

        $segment = Mage::registry('current_customer_segment');
        if ($segment && $segment->getId()) {
            $customersQty = Mage::getModel('Enterprise_CustomerSegment_Model_Segment')->getResource()
                ->getSegmentCustomersQty($segment->getId());
            $this->addTab('customers_tab', array(
                'label' => Mage::helper('Enterprise_CustomerSegment_Helper_Data')->__('Matched Customers (%d)', $customersQty),
                'url'   => $this->getUrl('*/report_customer_customersegment/customerGrid',
                    array('segment_id' => $segment->getId())),
                'class' => 'ajax',
            ));
        }

        return parent::_beforeToHtml();
    }

}
