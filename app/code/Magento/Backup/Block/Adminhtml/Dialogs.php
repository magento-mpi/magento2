<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backup\Block\Adminhtml;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Adminhtml rollback dialogs block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Dialogs extends \Magento\Backend\Block\Template
{
    /**
     * Block's template
     *
     * @var string
     */
    protected $_template = 'Magento_Backup::backup/dialogs.phtml';

    /**
     * Include backup.js file in page before rendering
     *
     * @return void
     * @see AbstractBlock::_prepareLayout()
     */
    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock(
            'head'
        )->addChild(
            'magento-adminhtml-backup-js',
            'Magento\Theme\Block\Html\Head\Script',
            array('file' => 'mage/adminhtml/backup.js')
        );
        parent::_prepareLayout();
    }
}
