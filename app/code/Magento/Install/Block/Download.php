<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Download Magento core modules and updates choice (online, offline)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block;

use Magento\Framework\App\Filesystem\DirectoryList;

class Download extends \Magento\Install\Block\AbstractBlock
{
    /**
     * @var string
     */
    protected $_template = 'download.phtml';

    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $installWizard
     * @param \Magento\Framework\Session\Generic $session
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $installWizard,
        \Magento\Framework\Session\Generic $session,
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        array $data = array()
    ) {
        parent::__construct($context, $installer, $installWizard, $session, $data);
        $this->_moduleReader = $moduleReader;
    }

    /**
     * Retrieve locale data post url
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('*/*/downloadPost');
    }

    /**
     * @return string
     */
    public function getNextUrl()
    {
        return $this->_installWizard->getStepByName('download')->getNextUrl();
    }

    /**
     * @return bool
     */
    public function hasLocalCopy()
    {
        $path = $this->_moduleReader->getModuleDir('etc', 'Magento_Adminhtml');
        $directory = $this->_filesystem->getDirectoryRead(DirectoryList::MODULES_DIR);

        if ($path && $directory->isDirectory($directory->getRelativePath($path))) {
            return true;
        }
        return false;
    }
}
