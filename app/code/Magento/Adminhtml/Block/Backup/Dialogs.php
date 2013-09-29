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
namespace Magento\Adminhtml\Block\Backup;

class Dialogs extends \Magento\Adminhtml\Block\Template
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
     * @see \Magento\Core\Block\AbstractBlock::_prepareLayout()
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->addChild(
            'magento-adminhtml-backup-js',
            'Magento\Page\Block\Html\Head\Script',
            array(
                'file' => 'mage/adminhtml/backup.js'
            )
        );
        parent::_prepareLayout();
    }
}
