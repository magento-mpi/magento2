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
 * Staging Manage Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('enterpriseStagingManageGrid');
        $this->setDefaultSort('name');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);

        //$this->setTemplate('enterprise/staging/manage/grid.phtml');
    }

    /**
     * PrepareCollection method.
     */

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('enterprise_staging/staging_collection');
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
            'type'      => 'date',
        ));

        $this->addColumn('apply_date', array(
            'header'    => 'Apply Date',
            'index'     => 'apply_date',
            'type'      => 'date',
        ));

        $this->addColumn('auto_apply_is_active', array(
            'header'    => 'Auto Apply Is Active',
            'index'     => 'auto_apply_is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('eav/entity_attribute_source_boolean')->getOptionArray()
        ));

        $this->addColumn('rollback_date', array(
            'header'    => 'Rollback Date',
            'index'     => 'rollback_date',
            'type'      => 'date',
        ));

        $this->addColumn('auto_rollback_is_active', array(
            'header'    => 'Auto Rollback Is Active',
            'index'     => 'auto_rollback_is_active',
            'type'      => 'options',
            'options'   => Mage::getSingleton('eav/entity_attribute_source_boolean')->getOptionArray()
        ));

        $this->addColumn('type', array(
            'header'    => 'Type',
            'index'     => 'type',
            'type'      => 'options',
            'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('type')
        ));

        $this->addColumn('state', array(
            'header'    => 'State',
            'index'     => 'state',
            'type'      => 'options',
            'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('state')
        ));

        $this->addColumn('status', array(
            'header'    => 'Status',
            'index'     => 'status',
            'type'      => 'options',
            'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('status')
        ));

        $this->addColumn('action',
            array(
                'header'    => Mage::helper('enterprise_staging')->__('Action'),
                'width'     => '50px',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption' => Mage::helper('enterprise_staging')->__('Edit'),
                        'url'     => array(
                            'base'=>'*/*/edit',
                            'params'=>array('store'=>$this->getRequest()->getParam('store'))
                        ),
                        'field'   => 'id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
        ));

        return $this;
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
        return $this->getUrl('*/*/edit', array(
            'store' => $this->getRequest()->getParam('store'),
            'id'    => $row->getId())
        );
    }
}