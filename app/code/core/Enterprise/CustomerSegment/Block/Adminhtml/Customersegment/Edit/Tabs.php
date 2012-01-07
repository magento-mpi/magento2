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
        $generalSectionContent = $this->getLayout()
            ->createBlock('enterprise_customersegment/adminhtml_customersegment_edit_tab_general')
            ->toHtml();

        $this->addTab('general_section', array(
            'label'   => Mage::helper('enterprise_customersegment')->__('General Properties'),
            'title'   => Mage::helper('enterprise_customersegment')->__('General Properties'),
            'content' => $generalSectionContent,
            'active'  => true
        ));

        $conditionsSectionContent = $this->getLayout()
            ->createBlock('enterprise_customersegment/adminhtml_customersegment_edit_tab_conditions')
            ->toHtml();

        $this->addTab('conditions_section', array(
            'label'   => Mage::helper('enterprise_customersegment')->__('Conditions'),
            'title'   => Mage::helper('enterprise_customersegment')->__('Conditions'),
            'content' => $conditionsSectionContent,
        ));


        $segment = Mage::registry('current_customer_segment');

        if ($segment && $segment->getId()) {
            if ($segment->getApplyTo() != Enterprise_CustomerSegment_Model_Segment::APPLY_TO_VISITORS) {
                $customersQty = Mage::getModel('enterprise_customersegment/segment')->getResource()
                    ->getSegmentCustomersQty($segment->getId());
                $this->addTab('customers_tab', array(
                    'label' => Mage::helper('enterprise_customersegment')->__('Matched Customers (%d)', $customersQty),
                    'url'   => $this->getUrl('*/report_customer_customersegment/customerGrid',
                        array('segment_id' => $segment->getId())),
                    'class' => 'ajax',
                ));
            }
        }

        return parent::_beforeToHtml();
    }

}
