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
 * Staging backup edit tabs
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Backup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('enterprise_staging_backup_tabs');
        $this->setDestElementId('enterprise_staging_backup_form');
        $this->setTitle(Mage::helper('enterprise_staging')->__('Staging Backup Information'));
    }

    /**
     * Preparing global layout
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $this->addTab('backup_general_info', array(
            'label'     => Mage::helper('enterprise_staging')->__('Backup General Info'),
            'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_backup_edit_tabs_general')->toHtml(),
        ));
/*
        $this->addTab('staging_general_info', array(
            'label'     => Mage::helper('enterprise_staging')->__('Staging General Info'),
            'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_edit_tabs_general')->toHtml(),
        ));*/
/*
        $this->addTab('event_info', array(
            'label'     => Mage::helper('enterprise_staging')->__('Event'),
            'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_event')->unsetChild('back_button')->toHtml(),
        ));*/

        $this->addTab('rollbacks_info', array(
            'label'     => Mage::helper('enterprise_staging')->__('Rollback History'),
            'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_backup_edit_tabs_rollback')->toHtml(),
        ));

        $this->addTab('rollback', array(
            'label'     => Mage::helper('enterprise_staging')->__('Rollback'),
            'content'   => $this->getLayout()->createBlock('enterprise_staging/manage_staging_rollback_settings_website')->toHtml(),
        ));
        
        return parent::_prepareLayout();
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
     * Retrive event object
     *
     * @return Enterprise_Staging_Model_Staging_Event
     */
    public function getEvent()
    {
        if (!($this->getData('staging_event') instanceof Enterprise_Staging_Model_Staging_Event)) {
            $this->setData('staging_event', Mage::registry('staging_event'));
        }
        return $this->getData('staging_event');
    }

    /**
     * Retrive staging object
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
