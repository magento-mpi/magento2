<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Staging backup edit block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Backup_Edit extends Mage_Adminhtml_Block_Widget
{

    protected $_template = 'backup/edit.phtml';

    protected function _construct()
    {
        parent::_construct();

        $this->setId('enterprise_staging_backup_edit');

        $this->setEditFormJsObject('enterpriseStagingBackupForm');
    }

    /**
     * Retrieve current Staging backup object
     *
     * @return Enterprise_Staging_Model_Staging_Backup
     */
    public function getBackup()
    {
        if (!($this->getData('staging_backup') instanceof Enterprise_Staging_Model_Staging_Action)) {
            $this->setData('staging_backup', Mage::registry('staging_backup'));
        }
        return $this->getData('staging_backup');
    }

    /**
     * Prepare output layout
     */
    protected function _prepareLayout()
    {
         if ($this->getBackup()->canRollback()) {
            $this->setChild('rollback_button',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                    ->setData(array(
                        'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Rollback'),
                        'onclick'   => 'enterpriseRollbackForm.submit()',
                        'class'  => 'add'
                    ))
            );
        } else {
            $this->setChild('rollback_button',
                $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                    ->setData(array(
                        'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Rollback'),
                        'class'  => 'disabled'
                    ))
            );
        }

        $this->setChild('back_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Back'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/').'\')',
                    'class' => 'back'
                ))
        );

        $this->setChild('reset_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Reset'),
                    'onclick'   => 'setLocation(\''.$this->getUrl('*/*/*', array('_current'=>true)).'\')'
                ))
        );

        $this->setChild('delete_button',
            $this->getLayout()->createBlock('Mage_Adminhtml_Block_Widget_Button')
                ->setData(array(
                    'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Delete'),
                    'onclick'   => 'confirmSetLocation(\''.Mage::helper('Enterprise_Staging_Helper_Data')->__('Are you sure?').'\', \''.$this->getDeleteUrl().'\')',
                    'class'  => 'delete'
                ))
        );

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
     * Return Delete Url
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', array('_current'=>true));
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
        return $this->escapeHtml($this->getBackup()->getName());
    }

    /**
     * Return Selected table id
     */
    public function getSelectedTabId()
    {
        return addslashes(htmlspecialchars($this->getRequest()->getParam('tab')));
    }
}
