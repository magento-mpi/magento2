<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Admin Actions Log Archive grid
 *
 */
class Enterprise_Logging_Block_Adminhtml_Details_Grid extends Magento_Adminhtml_Block_Widget_Grid
{
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
     * @return Enterprise_Logging_Block_Events_Archive_Grid
     */
    protected function _prepareCollection()
    {
        $event = Mage::registry('current_event');
        $collection = Mage::getResourceModel('Enterprise_Logging_Model_Resource_Event_Changes_Collection')
            ->addFieldToFilter('event_id', $event->getId());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_Logging_Block_Events_Archive_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('source_name', array(
            'header'    => __('Source Data'),
            'sortable'  => false,
            'renderer'  => 'Enterprise_Logging_Block_Adminhtml_Details_Renderer_Sourcename',
            'index'     => 'source_name',
            'width'     => 1
        ));

        $this->addColumn('original_data', array(
            'header'    => __('Value Before Change'),
            'sortable'  => false,
            'renderer'  => 'Enterprise_Logging_Block_Adminhtml_Details_Renderer_Diff',
            'index'     => 'original_data'
        ));

        $this->addColumn('result_data', array(
            'header'    => __('Value After Change'),
            'sortable'  => false,
            'renderer'  => 'Enterprise_Logging_Block_Adminhtml_Details_Renderer_Diff',
            'index'     => 'result_data'
        ));

        return parent::_prepareColumns();
    }
}
