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

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\CustomerSegment\Model\SegmentFactory
     */
    protected $_segmentFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\CustomerSegment\Model\SegmentFactory $segmentFactory
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\CustomerSegment\Model\SegmentFactory $segmentFactory,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_segmentFactory = $segmentFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $jsonEncoder, $authSession, $data);
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
                $customersQty = $this->_segmentFactory->create()
                    ->getResource()
                    ->getSegmentCustomersQty($segment->getId());
                $this->addTab('customers_tab', array(
                    'label' => __('Matched Customers (%1)', $customersQty),
                    'url'   => $this->getUrl('customersegment/report_customer_customersegment/customerGrid',
                        array('segment_id' => $segment->getId())),
                    'class' => 'ajax',
                ));
            }
        }

        return parent::_beforeToHtml();
    }
}
