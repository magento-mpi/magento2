<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml backup page content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Backup extends Magento_Adminhtml_Block_Template
{
    /**
     * Block's template
     *
     * @var string
     */
    protected $_template = 'backup/list.phtml';

    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addChild('createButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Database Backup'),
            'onclick' => "return backup.backup('" . Magento_Backup_Helper_Data::TYPE_DB . "')",
            'class'  => 'task'
        ));
        $this->addChild('createSnapshotButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('System Backup'),
            'onclick' => "return backup.backup('" . Magento_Backup_Helper_Data::TYPE_SYSTEM_SNAPSHOT . "')",
            'class'  => ''
        ));
        $this->addChild('createMediaBackupButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label' => __('Database and Media Backup'),
            'onclick' => "return backup.backup('" . Magento_Backup_Helper_Data::TYPE_MEDIA . "')",
            'class'  => ''
        ));

        $this->addChild('dialogs', 'Magento_Adminhtml_Block_Backup_Dialogs');
    }

    public function getCreateButtonHtml()
    {
        return $this->getChildHtml('createButton');
    }

    /**
     * Generate html code for "Create System Snapshot" button
     *
     * @return string
     */
    public function getCreateSnapshotButtonHtml()
    {
        return $this->getChildHtml('createSnapshotButton');
    }

    /**
     * Generate html code for "Create Media Backup" button
     *
     * @return string
     */
    public function getCreateMediaBackupButtonHtml()
    {
        return $this->getChildHtml('createMediaBackupButton');
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('backupsGrid');
    }

    /**
     * Generate html code for pop-up messages that will appear when user click on "Rollback" link
     *
     * @return string
     */
    public function getDialogsHtml()
    {
        return $this->getChildHtml('dialogs');
    }
}
