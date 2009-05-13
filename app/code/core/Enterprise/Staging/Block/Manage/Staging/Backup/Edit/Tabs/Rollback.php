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
                'long2ip' => 'enterprise_staging/widget_grid_column_renderer_ip'
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
        $collection = Mage::getResourceModel('enterprise_staging/staging_rollback_collection')
            ->setBackupFilter($this->getBackup())
            ->addEventToCollection();

        foreach($collection as $item) {
            $user = Mage::getModel('admin/user')->load($item->getEventUserId());

            $collection->getItemById($item->getId())
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
            'type'      => 'long2ip',
            'filter'    => 'enterprise_staging/widget_grid_column_filter_ip',
            'sortable'  => false
        ));

        $this->addColumn('loginname', array(
            'header'    => $this->helper->__('Username'),
            'index'     => 'loginname',
            'type'      => 'text',
            'sortable'  => false,
            'filter'    => false
        ));

        $this->addColumn('event_comment', array(
            'header'    => $this->helper->__('Comment'),
            'align'     => 'left',
            'index'     => 'event_comment',
            'type'      => 'text',
            'truncate'  => 50,
            'nl2br'     => true,
            'escape'    => true,
            'sortable'  => false,
            'filter'    => false
        ));

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
        return '';
    }

    /**
     * Retrieve current backup object
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
}
