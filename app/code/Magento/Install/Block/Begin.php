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
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Install\Model\Installer $installer
     * @param \Magento\Install\Model\Wizard $installWizard
     * @param \Magento\Core\Model\Session\Generic $session
     * @param array $data
     * @param string|null $eulaFile
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Install\Model\Installer $installer,
        \Magento\Install\Model\Wizard $installWizard,
        \Magento\Core\Model\Session\Generic $session,
        $eulaFile = null,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $installer, $installWizard, $session, $data);
        $this->_eulaFile = $eulaFile;
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
        return ($this->_eulaFile) ? $this->_filesystem->read(BP . DS . $this->_eulaFile) : '';
    }
}
