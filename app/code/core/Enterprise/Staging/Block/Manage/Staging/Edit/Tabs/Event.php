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
 * Staging events history tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Event extends Mage_Adminhtml_Block_Widget_Grid
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

        $this->setId('enterpriseStagingEventHistoryGrid');
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
     * Prepare staging events grid collection (add staging filter into collection)
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Event
     */
    protected function _prepareCollection()
    {
        if ($this->getCollection()){
            $collection = $this->getCollection(); 
        } else {
            $collection = Mage::getResourceModel('enterprise_staging/staging_event_collection');
            $collection->setStagingFilter($this->getStaging());
            $this->setCollection($collection);
        }
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
            'header'    => $this->helper->__('Date/Time'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => '150px'
        ));

        $this->addColumn('ip', array(
            'header'    => $this->helper->__('IP'),
            'index'     => 'ip',
            'type'    => 'long2ip'
        ));

        $this->addColumn('code', array(
            'header'    => $this->helper->__('Event Code'),
            'width'     => '100px',        
            'index'     => 'code',
            'type'      => 'options',
            'options'   => $this->_getEventCodeArray()
        ));

        $this->addColumn('username', array(
            'header'    => $this->helper->__('Login'),
            'index'     => 'username',
            'type'      => 'text'
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

        $this->addColumn('status', array(
            'header'    => $this->helper->__('Status'),
            'index'     => 'status',
            'type'      => 'text',
            'options'   => Enterprise_Staging_Model_Staging_Config::getOptionArray('status')
        ));

        $this->addColumn('comment', array(
            'header'        => $this->helper->__('Comment'),
            'align'         => 'left',
            'index'         => 'comment',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
            'sortable'      => false,
            'filter'        => false
        ));
/*
        $this->addColumn('log', array(
            'header'        => $this->helper->__('Log'),
            'align'         => 'left',
            'index'         => 'log',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
            'sortable'      => false,
            'filter'        => false
        ));*/

        return parent::_prepareColumns();
    }

    
    protected function _getEventCodeArray()
    {
        $eventCodes = array();
        
        $collection =  Mage::getResourceModel('enterprise_staging/staging_event_collection');
        $collection->setStagingFilter($this->getStaging());
        $this->setCollection($collection);
        
        if ($collection) {
            foreach($collection as $item){
                $eventCodes[$item->getCode()] = $item->getCode();         
            }
        }
        return $eventCodes;
    }
    
    /**
     * Return Url for "Only Grid" retrieves
     *
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('*/*/eventGrid', array('id' => $this->getStaging()->getId()));
    }

    /**
     * Return url for row events (onclick, etc)
     */
    public function getRowUrl($row)
    {
        //customisation
        if ($row->getCode()=='backup') {
            $backupId = $row->getBackupId();
            if ($backupId > 0) {
                return $this->getUrl('*/*/backupEdit', array(
                    'id' => $backupId 
                ));
            }
        }
        
        return $this->getUrl('*/*/event', array(
            'id' => $row->getId()
        ));
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
