<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging History Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Log_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('enterpriseStagingHistoryGrid');
        $this->setDefaultSort('log_id');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * PrepareCollection method.
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('Enterprise_Staging_Model_Resource_Staging_Log_Collection');
        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
         $this->addColumn('log_id', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('ID'),
            'index'     => 'log_id',
            'type'      => 'number'
        ));
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Logged At'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => 200
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Action'),
            'index'     => 'action',
            'type'      => 'options',
            'options'   => Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')->getActionLabelsArray(),
            'width' => 200
        ));

        $this->addColumn('from', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Website From'),
            'index'     => 'master_website_name',
            'type'      => 'text',
            'renderer' => 'Enterprise_Staging_Block_Adminhtml_Log_Grid_Renderer_Website',
            'width'     => 300
        ));

        $this->addColumn('to', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Website To'),
            'index'     => 'staging_website_name',
            'type'      => 'text',
            'renderer' => 'Enterprise_Staging_Block_Adminhtml_Log_Grid_Renderer_Website',
            'width'     => 300
        ));

        $this->addColumn('status', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Result'),
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Mage::getSingleton('Enterprise_Staging_Model_Staging_Config')->getStatusLabelsArray(),
            'width'  => 100
        ));



        return $this;
    }

    /**
     * Return Row Url
     */
    public function getRowUrl($row)
    {
        if (($row->getStagingWebsiteId() === null && $row->getStagingWebsiteName() !== null) || ($row->getMasterWebsiteId() === null && $row->getMasterWebsiteName() !== null)) {
            return false;
        }
        return $this->getUrl('*/*/view', array(
            'id' => $row->getId())
        );
    }
}
