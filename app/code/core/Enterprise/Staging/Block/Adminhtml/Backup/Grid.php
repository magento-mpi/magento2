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
 * Staging Backup Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Backup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('enterpriseStagingBackupGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');

        $this->setUseAjax(true);
        $this->setMassactionBlock("vailable");

        $this->setColumnRenderers(
            array(
                'action' => 'Enterprise_Staging_Block_Adminhtml_Widget_Grid_Column_Renderer_Action'
        ));
    }

    /**
     * Configuration of grid
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Backup_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Website'),
            'index'     => 'name',
            'type'      => 'text',
            'sortable'  => false
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Created At'),
            'index'     => 'created_at',
            'filter_index' => 'main_table.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('Enterprise_Staging_Helper_Data')->__('Action'),
            'type'      => 'action',
            'getter'    => 'getId',
            'width'     => 80,
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'type',
            'link_type' => 'actions',
            'actions'   => array(
                array(
                    'url'       => $this->getUrl('*/*/edit', array('id' => '$action_id')),
                    'caption'   => Mage::helper('Enterprise_Staging_Helper_Data')->__('Edit')
                ),
                array(
                    'url'       => $this->getUrl('*/*/delete', array('id' => '$action_id')),
                    'caption'   => Mage::helper('Enterprise_Staging_Helper_Data')->__('Delete'),
                    'confirm'   => Mage::helper('Enterprise_Staging_Helper_Data')->__('Are you sure you want to do this?')
                )
            )
        ));

        return $this;
    }

    /**
     *  Prepare mass action block
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Backup_Grid
     */
    protected function _prepareMassaction()
    {
        $this->setMassactionIdField('action_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->setNoFilterMassactionColumn(true);
        $this->getMassactionBlock()->setFormFieldName('backupDelete');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('Enterprise_Staging_Helper_Data')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('Enterprise_Staging_Helper_Data')->__('Are you sure?')
        ));
        return $this;
    }


    /**
     * prepare used website list
     *
     * @return array
     */
    protected function _getWebsiteList()
    {
        $collection = $this->getCollection();

        $websites = array();

        foreach($collection as $backup) {
            $websiteId   = $backup->getMasterWebsiteId();
            $websiteName = $backup->getMasterWebsiteName();
            $websites[$websiteId] = $websiteName;
        }

        return $websites;
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    /**
     * Return grid row url
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/edit', array('_current'=>true, 'id'=>$row->getId()));
    }

    /**
     * Prepare action/backup collection
     * used in such way instead of standard _prepareCollection
     * bc we need collection preloaded in _prepareColumns
     *
     * @return Enterprise_Staging_Model_Resource_Staging_Action_Collection
     */
    public function getCollection()
    {
        if (!$this->hasData('collection')) {
            $collection = Mage::getResourceModel('Enterprise_Staging_Model_Resource_Staging_Action_Collection')
                ->addFieldToFilter('type', 'backup')
                ->addWebsitesToCollection();
            $this->setData('collection', $collection);
        }
        return $this->getData('collection');
    }
}
