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
class Enterprise_Logging_Block_Adminhtml_Archive_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Initialize default sorting and html ID
     */
    protected function _construct()
    {
        $this->setId('loggingArchiveGrid');
        $this->setDefaultSort('basename');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    /**
     * Prepare grid collection
     *
     * @return Enterprise_Logging_Block_Events_Archive_Grid
     */
    protected function _prepareCollection()
    {
        $this->setCollection(Mage::getSingleton('Enterprise_Logging_Model_Archive_Collection'));
        return parent::_prepareCollection();
    }

    /**
     * Prepare grid columns
     *
     * @return Enterprise_Logging_Block_Events_Archive_Grid
     */
    protected function _prepareColumns()
    {
        $downloadUrl = $this->getUrl('*/*/download');

        $this->addColumn('download', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('Archive File'),
            'format'    => '<a href="' . $downloadUrl .'basename/$basename/">$basename</a>',
            'index'     => 'basename',
        ));

        $this->addColumn('date', array(
            'header'    => Mage::helper('Enterprise_Logging_Helper_Data')->__('Date'),
            'type'      => 'date',
            'index'     => 'time',
            'filter'    => 'Enterprise_Logging_Block_Adminhtml_Archive_Grid_Filter_Date'
        ));

        return parent::_prepareColumns();
    }

    /**
     * Row click callback URL
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/archiveGrid', array('_current' => true));
    }
}
