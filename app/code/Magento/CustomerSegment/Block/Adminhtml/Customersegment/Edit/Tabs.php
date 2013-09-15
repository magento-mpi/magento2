<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit;

class Tabs extends \Magento\Adminhtml\Block\Widget\Tabs
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
     * @return \Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit\Tabs
     */
    protected function _beforeToHtml()
    {
        $generalSectionContent = $this->getLayout()
            ->createBlock('Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit\Tab\General')
            ->toHtml();

        $this->addTab('general_section', array(
            'label'   => __('General Properties'),
            'title'   => __('General Properties'),
            'content' => $generalSectionContent,
            'active'  => true
        ));

        $segment = $this->_coreRegistry->registry('current_customer_segment');

        if ($segment && $segment->getId()) {
            $conditionsSectionContent = $this->getLayout()
                ->createBlock('Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit\Tab\Conditions')
                ->toHtml();

            $this->addTab('conditions_section', array(
                'label'   => __('Conditions'),
                'title'   => __('Conditions'),
                'content' => $conditionsSectionContent,
            ));

            if ($segment->getApplyTo() != \Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS) {
                $customersQty = \Mage::getModel('Magento\CustomerSegment\Model\Segment')->getResource()
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
