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
    public function __construct()
    {
        parent::__construct();

        $this->setId('enterpriseStagingHistoryGrid');
        $this->setDefaultSort('created_at');
        $this->setDefaultDir('DESC');
        $this->setSaveParametersInSession(true);
    }

    /**
     * PrepareCollection method.
     */
    protected function _prepareCollection()
    {
        $collection = Mage::getResourceModel('enterprise_staging/staging_log_collection')
            ->addFieldToFilter('code', array('in' => array(
                Enterprise_Staging_Model_Staging_Config::PROCESS_CREATE,
                Enterprise_Staging_Model_Staging_Config::PROCESS_MERGE,
                Enterprise_Staging_Model_Staging_Config::PROCESS_ROLLBACK)))
            ->addFieldToFilter('status', array('neq' => Enterprise_Staging_Model_Staging_Config::STATUS_FAIL));

        $this->setCollection($collection);

        parent::_prepareCollection();

        return $this;
    }

    /**
     * Configuration of grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('created_at', array(
            'header'    => Mage::helper('enterprise_staging')->__('Logged At'),
            'index'     => 'created_at',
            'type'      => 'datetime',
            'width'     => 200
        ));

        $this->addColumn('from', array(
            'header'    => Mage::helper('enterprise_staging')->__('From'),
            'index'     => 'master_website_id',
            'type'      => 'options',
            'options'   => $this->getWebsites(),
            'width'     => 300
        ));

        $this->addColumn('to', array(
            'header'    => Mage::helper('enterprise_staging')->__('To'),
            'index'     => 'staging_website_id',
            'type'      => 'options',
            'options'   => $this->getWebsites(),
            'width'     => 300
        ));

        $this->addColumn('action', array(
            'header'    => Mage::helper('enterprise_staging')->__('Action'),
            'index'     => 'name',
            'type'      => 'text',
            'truncate'  => 1000
        ));

        return $this;
    }

    /**
     * Prepare array of core website ans also one wich will be associated with Backup
     *
     * @return array
     */
    public function getWebsites()
    {
        $coreWebsites = Mage::getModel('core/website')->getCollection()->toOptionHash();
        $coreWebsites['0'] = Mage::helper('enterprise_staging')->__('Backup');
        return $coreWebsites;
    }

    /**
     * Return Row Url
     */
    public function getRowUrl($row)
    {
        return $this->getUrl('*/*/view', array(
            'id' => $row->getId())
        );
    }
}
