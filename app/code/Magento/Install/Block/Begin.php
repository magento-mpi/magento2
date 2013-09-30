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
class Magento_Install_Block_Begin extends Magento_Install_Block_Abstract
{
    protected $_template = 'begin.phtml';

    /**
     * Eula file name
     *
     * @var string
     */
    protected $_eulaFile;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Install_Model_Installer $installer
     * @param Magento_Install_Model_Wizard $installWizard
     * @param Magento_Core_Model_Session_Generic $session
     * @param array $data
     * @param string|null $eulaFile
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Install_Model_Installer $installer,
        Magento_Install_Model_Wizard $installWizard,
        Magento_Core_Model_Session_Generic $session,
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
