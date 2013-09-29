<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Configuration for reports
 */
class Magento_Reports_Model_Config extends Magento_Object
{
    /**
     * @var Magento_Core_Model_Config_Modules_Reader
     */
    protected $_moduleReader;

    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Config_Modules_Reader $moduleReader
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Config_Modules_Reader $moduleReader,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_moduleReader = $moduleReader;
        $this->_storeManager = $storeManager;
    }

    public function getGlobalConfig()
    {
        $dom = new DOMDocument();
        $dom->load($this->_moduleReader->getModuleDir('etc', 'Magento_Reports') . DS . 'flexConfig.xml');

        $baseUrl = $dom->createElement('baseUrl');
        $baseUrl->nodeValue = $this->_storeManager->getBaseUrl();

        $dom->documentElement->appendChild($baseUrl);

        return $dom->saveXML();
    }

    public function getLanguage()
    {
        return file_get_contents(
            $this->_moduleReader->getModuleDir('etc', 'Magento_Reports') . DS . 'flexLanguage.xml'
        );
    }

    public function getDashboard()
    {
        return file_get_contents(
            $this->_moduleReader->getModuleDir('etc', 'Magento_Reports') . DS . 'flexDashboard.xml'
        );
    }
}
