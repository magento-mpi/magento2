<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Download Magento core modules and updates choice (online, offline)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Install\Block;

class Download extends \Magento\Install\Block\AbstractBlock
{
    /**
     * @var string
     */
    protected $_template = 'download.phtml';

    /**
     * @var \Magento\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $installWizard
     * @param \Magento\Session\Generic $session
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $installWizard,
        \Magento\Session\Generic $session,
        \Magento\Module\Dir\Reader $moduleReader,
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
        return $this->_installWizard
            ->getStepByName('download')
            ->getNextUrl();
    }

    /**
     * @return bool
     */
    public function hasLocalCopy()
    {
        $path = $this->_moduleReader->getModuleDir('etc', 'Magento_Adminhtml');
        $directory = $this->_filesystem->getDirectoryRead(\Magento\App\Filesystem::MODULES_DIR);

        if ($path && $directory->isDirectory($directory->getRelativePath($path))) {
            return true;
        }
        return false;
    }
}
