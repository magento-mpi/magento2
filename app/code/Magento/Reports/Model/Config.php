<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model;

/**
 * Configuration for reports
 */
class Config extends \Magento\Object
{
    /**
     * @var \Magento\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Module\Dir\Reader $moduleReader
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Module\Dir\Reader $moduleReader,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        parent::__construct($data);
        $this->_moduleReader = $moduleReader;
        $this->_storeManager = $storeManager;
    }

    /**
     * Return reports global configuration
     *
     * @return string
     */
    public function getGlobalConfig()
    {
        $dom = new \DOMDocument();
        $dom->load($this->_moduleReader->getModuleDir('etc', 'Magento_Reports') . '/flexConfig.xml');

        $baseUrl = $dom->createElement('baseUrl');
        $baseUrl->nodeValue = $this->_storeManager->getBaseUrl();

        $dom->documentElement->appendChild($baseUrl);

        return $dom->saveXML();
    }

    /**
     * Return reports language
     *
     * @return string
     */
    public function getLanguage()
    {
        return file_get_contents($this->_moduleReader->getModuleDir('etc', 'Magento_Reports') . '/flexLanguage.xml');
    }

    /**
     * Return reports dashboard
     *
     * @return string
     */
    public function getDashboard()
    {
        return file_get_contents($this->_moduleReader->getModuleDir('etc', 'Magento_Reports') . '/flexDashboard.xml');
    }
}
