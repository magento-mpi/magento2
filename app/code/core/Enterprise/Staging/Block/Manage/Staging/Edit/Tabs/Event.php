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
 * Staging event history tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Edit_Tabs_Event extends Mage_Adminhtml_Block_Widget_Grid
{
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
    }

    /**
     * PrepareCollection method.
     */

    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('enterprise_staging/staging_event_collection');
        $collection->setStagingFilter($this->getStaging());
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

/**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('enterprise_staging')->__('Event Time'),
            'index'     => 'created_at',
            'type'      => 'datetime',
        ));

        $this->addColumn('ip', array(
            'header'    => Mage::helper('enterprise_staging')->__('IP'),
            'index'     => 'ip',
            'type'      => 'long2ip',
        ));

        $this->addColumn('code', array(
            'header'    => Mage::helper('enterprise_staging')->__('Event Code'),
            'index'     => 'code',
            'type'      => 'eventlabel',
            'sortable'  => false,
        ));

        $this->addColumn('username', array(
            'header'    => Mage::helper('enterprise_staging')->__('User Name'),
            'index'     => 'username',
            'type'      => 'text',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('enterprise_staging')->__('Action'),
            'index'     => 'action',
            'type'      => 'text',
            'sortable'  => true,
            'filter'    => false
        ));

        $this->addColumn('internal_status', array(
            'header'    => Mage::helper('enterprise_staging')->__('Result'),
            'index'     => 'internal_status',
            'type'      => 'text',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('comment', array(
            'header'        => Mage::helper('enterprise_staging')->__('Comment'),
            'align'         => 'left',
            'index'         => 'comment',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
        ));

        $this->addColumn('log', array(
            'header'        => Mage::helper('enterprise_staging')->__('Log'),
            'align'         => 'left',
            'index'         => 'log',
            'type'          => 'text',
            'truncate'      => 50,
            'nl2br'         => true,
            'escape'        => true,
        ));

        return $this;
    }

    /**
     * Return grids url
     */
    public function getGridUrl()
    {
        return $this->getUrl('adminhtml/staging_manage/eventGrid', array('_current'=>true));
    }

    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/eventEdit', array(
            'id'    => $row->getId())
        );
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