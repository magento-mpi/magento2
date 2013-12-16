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
 * Installation begin block
 */
namespace Magento\Install\Block;

class Begin extends \Magento\Install\Block\AbstractBlock
{
    protected $_template = 'begin.phtml';

    /**
     * Eula file name
     *
     * @var string
     */
    protected $_eulaFile;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $installWizard
     * @param \Magento\Session\Generic $session
     * @param null $eulaFile
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $installWizard,
        \Magento\Session\Generic $session,
        $eulaFile = null,
        array $data = array()
    ) {
        $this->_eulaFile = $eulaFile;
        parent::__construct($context, $installer, $installWizard, $session, $data);

    }

    /**
     * Get wizard URL
     *
     * @return string
     */
    public function getPostUrl()
    {
        return $this->getUrl('install/wizard/beginPost');
    }

    /**
     * Get License HTML contents
     *
     * @return string
     */
    public function getLicenseHtml()
    {
        return ($this->_eulaFile)
            ? $this->_filesystem->getDirectoryRead(\Magento\Filesystem::ROOT)->readFile($this->_eulaFile)
            : '';
    }
}
