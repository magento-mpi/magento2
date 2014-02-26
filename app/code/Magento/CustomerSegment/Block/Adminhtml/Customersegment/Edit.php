<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerSegment\Block\Adminhtml\Customersegment;

/**
 * Edit form for customer segment configuration
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Initialize form
     * Add standard buttons
     * Add "Refresh Segment Data" button
     * Add "Save and Continue" button
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Magento_CustomerSegment';

        parent::_construct();

        /** @var $segment \Magento\CustomerSegment\Model\Segment */
        $segment = $this->_coreRegistry->registry('current_customer_segment');
        if ($segment && $segment->getId()) {
            $this->_addButton('match_customers', array(
                'label'     => __('Refresh Segment Data'),
                'onclick'   => 'setLocation(\'' . $this->getMatchUrl() . '\')',
            ), -1);
        }

        $this->_addButton('save_and_continue_edit', array(
            'class'   => 'save',
            'label'   => __('Save and Continue Edit'),
            'data_attribute'  => array(
                'mage-init' => array(
                    'button' => array('event' => 'saveAndContinueEdit', 'target' => '#edit_form'),
                ),
            ),
        ), 3);
    }

    /**
     * Get url for run segment customers matching
     *
     * @return string
     */
    public function getMatchUrl()
    {
        $segment = $this->_coreRegistry->registry('current_customer_segment');
        return $this->getUrl('*/*/match', array('id'=>$segment->getId()));
    }

    /**
     * Getter for form header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        $segment = $this->_coreRegistry->registry('current_customer_segment');
        if ($segment->getSegmentId()) {
            return __("Edit Segment '%1'", $this->escapeHtml($segment->getName()));
        } else {
            return __('New Segment');
        }
    }
}
