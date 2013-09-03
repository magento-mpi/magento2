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
 * Adminhtml rollback dialogs block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Backup_Dialogs extends Magento_Adminhtml_Block_Template
{
    /**
     * Block's template
     *
     * @var string
     */
    protected $_template = 'backup/dialogs.phtml';

    /**
     * Include backup.js file in page before rendering
     *
     * @see Magento_Core_Block_Abstract::_prepareLayout()
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addChild(
            'magento-adminhtml-backup-js',
            'Magento_Page_Block_Html_Head_Script',
            array(
                'file' => 'mage/adminhtml/backup.js'
            )
        );
        parent::_prepareLayout();
    }
}
