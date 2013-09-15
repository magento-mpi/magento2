<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Edit form for customer segment configuration
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Block_Adminhtml_Customersegment_Edit extends Magento_Adminhtml_Block_Widget_Form_Container
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
     * Initialize form
     * Add standard buttons
     * Add "Refresh Segment Data" button
     * Add "Save and Continue" button
     */
    protected function _construct()
    {
        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_customersegment';
        $this->_blockGroup = 'Magento_CustomerSegment';

        parent::_construct();

        /** @var $segment Magento_CustomerSegment_Model_Segment */
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
