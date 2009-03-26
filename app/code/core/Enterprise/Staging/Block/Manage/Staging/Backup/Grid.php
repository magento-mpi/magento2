<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE_AFL.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
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
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

        $this->setTemplate('enterprise/staging/manage/staging/backup/grid.phtml');
    }

    /**
     * PrepareCollection method.
     */

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('enterprise_staging/staging_backup_collection');
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('name', array(
            'header'    => 'Name',
            'index'     => 'name',
            'type'      => 'text',
        ));

        $this->addColumn('created_at', array(
            'header'    => 'Created At',
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $actions = array();
        $actions[] = array(
            'caption' => Mage::helper('enterprise_staging')->__('Delete'),
            'url'     => array(
                'base'   =>'*/*/backupDelete',
                'params' =>array()
            ),
            'field'      => 'id'
        );

        $this->addColumn('action_delete',
            array(
                'header'    => Mage::helper('enterprise_staging')->__('Delete'),
                'width'     => '100px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => $actions,
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        return $this;
    }

    /**
     * Retrieve currently edited staging object
     *
     * @return Enterprise_Staging_Block_Manage_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/grid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/backupEdit', array('_current'=>true, 'id'=>$row->getId()));
    }
}