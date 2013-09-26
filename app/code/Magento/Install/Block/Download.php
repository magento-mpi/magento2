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
class Magento_Install_Block_Download extends Magento_Install_Block_Abstract
{
    /**
     * @var string
     */
    protected $_template = 'download.phtml';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Install_Model_Installer $installer
     * @param Magento_Install_Model_Wizard $installWizard
     * @param Magento_Core_Model_Session_Generic $session
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Install_Model_Installer $installer,
        Magento_Install_Model_Wizard $installWizard,
        Magento_Core_Model_Session_Generic $session,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $installer, $installWizard, $session, $data);
        $this->_coreConfig = $coreConfig;
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
        $dir = $this->_coreConfig->getModuleDir('etc', 'Magento_Adminhtml');
        if ($dir && $this->_filesystem->isDirectory($dir)) {
            return true;
        }
        return false;
    }
}
