<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Reports\Model;

/**
 * Configuration for reports
 */
class Config extends \Magento\Framework\Object
{
    /**
     * @var \Magento\Framework\Module\Dir\Reader
     */
    protected $_moduleReader;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Framework\Module\Dir\Reader $moduleReader
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $moduleReader,
        \Magento\Framework\StoreManagerInterface $storeManager,
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
