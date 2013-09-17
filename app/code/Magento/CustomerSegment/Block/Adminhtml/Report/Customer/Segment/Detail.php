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
 * Customer Segments Detail grid container
 *
 * @category   Magento
 * @package    Magento_CustomerSegment
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_CustomerSegment_Block_Adminhtml_Report_Customer_Segment_Detail
    extends Magento_Backend_Block_Widget_Grid_Container
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
     * Constructor
     *
     */
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_CustomerSegment';
        $this->_controller = 'adminhtml_report_customer_segment_detail';
        if ($this->getCustomerSegment() && $name = $this->getCustomerSegment()->getName()) {
            $title = __('Customer Segment Report \'%1\'', $this->escapeHtml($name));
        } else {
            $title = __('Customer Segments Report');
        }
        $pageTitleBlock = $this->getLayout()->getBlock('page-title');
        if ($pageTitleBlock) {
            $pageTitleBlock->setPageTitle($title);
        } else {
            $this->_headerText = $title;
        }

        parent::_construct();
        $this->_removeButton('add');
        $this->addButton('back', array(
            'label'     => __('Back'),
            'onclick'   => 'setLocation(\'' . $this->getBackUrl() .'\')',
            'class'     => 'back',
        ));
        $this->addButton('refresh', array(
            'label'     => __('Refresh Segment Data'),
            'onclick'   => 'setLocation(\'' . $this->getRefreshUrl() .'\')',
        ));
    }

    /**
     * Get URL for refresh button
     *
     * @return string
     */
    public function getRefreshUrl()
    {
        return $this->getUrl('*/*/refresh', array('_current' => true));
    }

    /**
     * Get URL for back button
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/segment');
    }

    /**
     * Getter
     *
     * @return Magento_CustomerSegment_Model_Segment
     */
    public function getCustomerSegment()
    {
        return $this->_coreRegistry->registry('current_customer_segment');
    }

    /**
     * Retrieve all websites
     *
     * @return array
     */
    public function getWebsites()
    {
        return Mage::app()->getWebsites();
    }
}
