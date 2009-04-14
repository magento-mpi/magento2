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
 * Staging backup edit block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Backup_Edit extends Mage_Adminhtml_Block_Widget
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/staging/manage/staging/backup/edit.phtml');
        $this->setId('enterprise_staging_backup_edit');

        $this->setEditFormJsObject('enterpriseStagingBackupForm');
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
     * Retrieve currently edited backup staging object
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

    /**
     * Prepare output layout
     */
    protected function _prepareLayout()
    {
        $this->setChild('back_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('enterprise_staging')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/backup').'\')',
                    'class' => 'back'
                ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('enterprise_staging')->__('Reset'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
                ))
        );

        if ($this->getStaging()->canDelete()) {
            $this->setChild('delete_button',
                $this->getLayout()->createBlock('adminhtml/widget_button')
                    ->setData(array(
                        'label'     => Mage::helper('enterprise_staging')->__('Delete'),
                        'onclick'   => 'confirmSetLocation(\''.Mage::helper('enterprise_staging')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
                        'class'  => 'delete'
                    ))
            );
        }

        return parent::_prepareLayout();
    }

    /**
     * Return backup button html
     */
    public function getBackButtonHtml()
    {
        return $this->getChildHtml('back_button');
    }

    /**
     * Return Cancel Button Html
     */    
    public function getCancelButtonHtml()
    {
        return $this->getChildHtml('reset_button');
    }
    
    /**
     * Return Delete Button Html
     */    
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }

    /**
     * Return Rolllback Button Html
     */    
    public function getRollbackButtonHtml()
    {
        return $this->getChildHtml('rollback_button');
    }

    /**
     * Return Validation url
     */    
    public function getValidationUrl()
    {
        return $this->getUrl('*/*/validate', array('_current'=>true));
    }

    /**
     * Return Staging Id
     */    
    public function getStagingId()
    {
        return $this->getStaging()->getId();
    }

    /**
     * Return Delete Url
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/backupDelete', array('_current'=>true));
    }
    
    /**
     * Return Rollback Url
     */
    public function getRollbackUrl()
    {
        return $this->getUrl('*/*/rollback', array('_current'=>true));
    }

    /**
     * Return Header
     */
    public function getHeader()
    {
        return $this->htmlEscape($this->getBackup()->getName());
    }

    /**
     * Return Selected table id
     */
    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }
}
