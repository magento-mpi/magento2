<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging backup rollbacks tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Backup_Edit_Tabs_Rollback extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Keeps main translate helper instance
     *
     * @var object Mage_Core_Helper_Abstract
     */
    protected $helper;

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setId('enterpriseStagingBackupRollbackGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
        
        $this->setColumnRenderers(
            array(
                'long2ip' => 'enterprise_staging/manage_staging_edit_renderer_ip'
            ));
        

        $this->helper = Mage::helper('enterprise_staging');
    }

    /**
     * Prepare staging backup rollbacks grid collection
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Backup_Edit_Tabs_Rollback
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('enterprise_staging/staging_rollback_collection');
        $collection->setBackupFilter($this->getBackup())->addEventToCollection()->getSelect();
        //$collection->addCompleteFilter();
        foreach($collection AS $datasetItem) {
            $user = Mage::getModel('admin/user')->load($datasetItem->getEventUserId());
            
            $collection->getItemById($datasetItem->getId())
                ->setData("loginname", $user->getUsername());
        }
        
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Columns Configuration
     *
     * @return object Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Event
     */
    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'    => $this->helper->__('Rollback Date'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => '150px',
            'filter'    => false
        
        ));

        $this->addColumn('event_ip', array(
            'header'    => $this->helper->__('IP'),
            'index'     => 'event_ip',
            'type'    => 'long2ip',
            'sortable'  => false,
            'filter'    => false            
        ));

        /*$this->addColumn('code', array(
            'header'    => $this->helper->__('Event Code'),
            'width'     => '100px',        
            'index'     => 'code',
            'type'      => 'options',
            'options'   => $this->_getEventCodeArray()
        ));*/

        $this->addColumn('loginname', array(
            'header'    => $this->helper->__('Username'),
            'index'     => 'loginname',
            'type'      => 'text',
            'sortable'  => false,            
            'filter'    => false
        ));

        /*$this->addColumn('action', array(
            'header'    => $this->helper->__('Action'),
            'index'     => 'action',
            'type'      => 'text'
        ));

        $this->addColumn('state', array(
            'header'    => $this->helper->__('State'),
            'index'     => 'state',
            'type'      => 'text',
            'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('state')
        )); */

        /*$this->addColumn('status', array(
            'header'    => $this->helper->__('Status'),
            'index'     => 'status',
            'type'      => 'text',
            'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('status')
        ));*/

        $this->addColumn('event_comment', array(
            'header'        => $this->helper->__('Comment'),
            'align'         => 'left',
            'index'         => 'event_comment',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
            'sortable'      => false,
            'filter'        => false
        ));
        
        /*$this->addColumn('state', array(
            'header'    => $this->helper->__('State'),
            'index'     => 'state',
            'type'      => 'text',
            'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('state')
        ));*/

        return parent::_prepareColumns();
    }

    /**
     * Return Url for "Only Grid" retrieves
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/rollbackGrid', array('_current'=>true));
    }
       
    /**
     * Return url for row events (onclick, etc)
     */
    public function getRowUrl($row)
    {
        return "";
        //return $this->getUrl('*/*/rollbackEdit', array(
        //    'id' => $row->getId()
        //));
    }

    /**
     * Retrieve currently edited backup object
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!($this->getData('staging_backup') instanceof Enterprise_Staging_Model_Staging_Backup)) {
            $this->setData('staging_backup', Mage::registry('staging_backup'));
        }
        return $this->getData('staging_backup');
    }

    /**
     * Retrieve currently edited staging object
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }
}
