<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Block\Adminhtml;

use Magento\View\Element\AbstractBlock;

/**
 * Adminhtml backup page content block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Backup extends \Magento\Backend\Block\Template
{
    /**
     * Block's template
     *
     * @var string
     */
    protected $_template = 'Magento_Backup::backup/list.phtml';

    /**
     * @return AbstractBlock|void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->addChild('createButton', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Database Backup'),
            'onclick' => "return backup.backup('" . \Magento\Backup\Factory::TYPE_DB . "')",
            'class'  => 'task'
        ));
        $this->addChild('createSnapshotButton', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('System Backup'),
            'onclick' => "return backup.backup('" . \Magento\Backup\Factory::TYPE_SYSTEM_SNAPSHOT . "')",
            'class'  => ''
        ));
        $this->addChild('createMediaBackupButton', 'Magento\Backend\Block\Widget\Button', array(
            'label' => __('Database and Media Backup'),
            'onclick' => "return backup.backup('" . \Magento\Backup\Factory::TYPE_MEDIA . "')",
            'class'  => ''
        ));

        $this->addChild('dialogs', 'Magento\Backup\Block\Adminhtml\Dialogs');
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
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
