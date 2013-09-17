<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin Actions Log Archive grid
 *
 */
class Magento_Logging_Block_Adminhtml_Details_Grid extends Magento_Adminhtml_Block_Widget_Grid
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
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Url $urlModel
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Url $urlModel,
        Magento_Core_Model_Registry $coreRegistry,
        array $data = array()
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($coreData, $context, $storeManager, $urlModel, $data);
    }

    /**
     * Initialize default sorting and html ID
     */
    protected function _construct()
    {
        $this->setId('loggingDetailsGrid');
        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);
    }

    /**
     * Prepare grid collection
     *
     * @return Magento_Logging_Block_Events_Archive_Grid
     */
    protected function _prepareCollection()
    {
        $event = $this->_coreRegistry->registry('current_event');
        $collection = Mage::getResourceModel('Magento_Logging_Model_Resource_Event_Changes_Collection')
            ->addFieldToFilter('event_id', $event->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Magento_Logging_Block_Events_Archive_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('source_name', array(
            'header'    => __('Source Data'),
            'sortable'  => false,
            'renderer'  => 'Magento_Logging_Block_Adminhtml_Details_Renderer_Sourcename',
            'index'     => 'source_name',
            'width'     => 1
        ));

        $this->addColumn('original_data', array(
            'header'    => __('Value Before Change'),
            'sortable'  => false,
            'renderer'  => 'Magento_Logging_Block_Adminhtml_Details_Renderer_Diff',
            'index'     => 'original_data'
        ));

        $this->addColumn('result_data', array(
            'header'    => __('Value After Change'),
            'sortable'  => false,
            'renderer'  => 'Magento_Logging_Block_Adminhtml_Details_Renderer_Diff',
            'index'     => 'result_data'
        ));

        return parent::_prepareColumns();
    }
}
