<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tabs extends Magento_Adminhtml_Block_Widget_Tabs
{

    /**
     * Intialize form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('magento_customersegment_segment_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Segment Information'));
    }

    /**
     * Add tab sections
     *
     * @return Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tabs
     */
    protected function _beforeToHtml()
    {
        $generalSectionContent = $this->getLayout()
            ->createBlock('Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_General')
            ->toHtml();

        $this->addTab('general_section', array(
            'label'   => __('General Properties'),
            'title'   => __('General Properties'),
            'content' => $generalSectionContent,
            'active'  => true
        ));

        $segment = Mage::registry('current_customer_segment');

        if ($segment && $segment->getId()) {
            $conditionsSectionContent = $this->getLayout()
                ->createBlock('Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit_Tab_Conditions')
                ->toHtml();

            $this->addTab('conditions_section', array(
                'label'   => __('Conditions'),
                'title'   => __('Conditions'),
                'content' => $conditionsSectionContent,
            ));

            if ($segment->getApplyTo() != Magento_CustomerSegment_Model_Segment::APPLY_TO_VISITORS) {
                $customersQty = Mage::getModel('Magento_CustomerSegment_Model_Segment')->getResource()
                    ->getSegmentCustomersQty($segment->getId());
                $this->addTab('customers_tab', array(
                    'label' => __('Matched Customers (%1)', $customersQty),
                    'url'   => $this->getUrl('*/report_customer_customersegment/customerGrid',
                        array('segment_id' => $segment->getId())),
                    'class' => 'ajax',
                ));
            }
        }

        return parent::_beforeToHtml();
    }

}
