<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Staging Backup Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Backup_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('enterpriseStagingBackupGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');

        $this->setUseAjax(true);
        $this->setMassactionBlock("vailable");

        $this->setTemplate('enterprise/staging/manage/staging/backup/grid.phtml');

        $this->setColumnRenderers(
            array(
                'action' => 'enterprise_staging/manage_staging_renderer_grid_column_action'
        ));
    }

    /**
     * Prepare collection data for grid
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Backup_Grid
     */
    protected function _prepareCollection()
    {
        if (!$this->getCollection()) {
            $collection = Mage::getResourceModel('enterprise_staging/staging_backup_collection')
                ->addStagingToCollection(true);
            $this->setCollection($collection);
        }

        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Backup_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('master_website_id', array(
            'header'    => Mage::helper('enterprise_staging')->__('Website'),
            'index'     => 'master_website_id',
            'filter_index' => 'core_website.website_id',
            'type'      => 'options',
            'options'   => $this->_getWebsiteList(),
            'sortable'  => false
        ));

        $this->addColumn('created_at', array(
            'header'    => Mage::helper('enterprise_staging')->__('Created At'),
            'index'     => 'created_at',
            'filter_index' => 'main_table.created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('enterprise_staging')->__('Action'),
            'type'      => 'action',
            'getter'     => 'getId',
            'width'     => '80px',
            'filter'    => false,
            'sortable'  => false,
            'index'     => 'type',
            'render_type' => 'link',
            'actions'   => array(
                array(
                    'url'       => $this->getUrl('*/*/edit', array('id' => '$backup_id')),
                    'caption'   => Mage::helper('enterprise_staging')->__('Edit')
                ),
                array(
                    'url'       => $this->getUrl('*/*/delete', array('id' => '$backup_id')),
                    'caption'   => Mage::helper('enterprise_staging')->__('Delete'),
                    'confirm'   => Mage::helper('enterprise_staging')->__('Are you sure you want to do this?')
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
        $this->setMassactionIdField('backup_id');
        $this->setMassactionIdFieldOnlyIndexValue(true);
        $this->setNoFilterMassactionColumn(true);
        $this->getMassactionBlock()->setFormFieldName('backupDelete');

        $this->getMassactionBlock()->addItem('delete', array(
            'label'   => Mage::helper('enterprise_staging')->__('Delete'),
            'url'     => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('enterprise_staging')->__('Are you sure?')
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
        if (!$this->getCollection()) {
            $collection = Mage::getResourceModel('enterprise_staging/staging_backup_collection')
                ->addStagingToCollection(true);
        } else {
            $collection = $this->getCollection();
        }

        $websites = array();

        foreach($collection AS $backup) {
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
}
