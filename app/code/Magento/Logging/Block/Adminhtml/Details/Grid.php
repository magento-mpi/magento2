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
namespace Magento\Logging\Block\Adminhtml\Details;

class Grid extends \Magento\Adminhtml\Block\Widget\Grid
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
     * @return Magento_Logging_Block_Events_Archive_Grid
     */
    protected function _prepareCollection()
    {
        $event = \Mage::registry('current_event');
        $collection = \Mage::getResourceModel('Magento\Logging\Model\Resource\Event\Changes\Collection')
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
            'renderer'  => 'Magento\Logging\Block\Adminhtml\Details\Renderer\Sourcename',
            'index'     => 'source_name',
            'width'     => 1
        ));

        $this->addColumn('original_data', array(
            'header'    => __('Value Before Change'),
            'sortable'  => false,
            'renderer'  => 'Magento\Logging\Block\Adminhtml\Details\Renderer\Diff',
            'index'     => 'original_data'
        ));

        $this->addColumn('result_data', array(
            'header'    => __('Value After Change'),
            'sortable'  => false,
            'renderer'  => 'Magento\Logging\Block\Adminhtml\Details\Renderer\Diff',
            'index'     => 'result_data'
        ));

        return parent::_prepareColumns();
    }
}
