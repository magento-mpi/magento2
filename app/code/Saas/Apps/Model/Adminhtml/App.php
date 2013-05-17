<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Apps
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml Application Data Provider
 *
 * @category    Saas
 * @package     Saas_Apps
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Apps_Model_Adminhtml_App extends Mage_Core_Model_Abstract
{
    /**
     * Apps proxy page cache key
     */
    const APPS_PROXY_PAGE_CACHE_KEY = 'apps_proxy_page_cache_key';

    /**
     * Apps proxy page cache ttl
     */
    const APPS_PROXY_PAGE_CACHE_TTL = 3600;

    /**
     * Apps helper
     *
     * @var Saas_Apps_Helper_Data
     */
    protected $_helper;

    /**
     * Curl http adapter
     *
     * @var Varien_Http_Adapter_Curl
     */
    protected $_curl;

    /**
     * Apps model constructor
     *
     * @param Mage_Core_Model_Context $context
     * @param Saas_Apps_Helper_Data $helper
     * @param Varien_Http_Adapter_Curl $curl
     */
    public function __construct(
        Mage_Core_Model_Context $context,
        Saas_Apps_Helper_Data $helper,
        Varien_Http_Adapter_Curl $curl
    ) {
        $this->_helper = $helper;
        $this->_curl = $curl;
        parent::__construct($context);
    }

    /**
     * Return contents or false
     *
     * @return bool|string
     */
    public function getContents()
    {
        $data = $this->_cacheManager->load(self::APPS_PROXY_PAGE_CACHE_KEY);
        if (!$data){
            $url  = $this->_helper->getAppTabUrl();
            if ($url) {
                $this->_curl->setConfig(array(
                    'timeout'   => 10,
                    'header'    => false,
                ));
                $this->_curl->addOption(CURLOPT_FOLLOWLOCATION, true);

                $this->_curl->write(Zend_Http_Client::GET, $url);
                $data = $this->_curl->read();

                if ($data !== false) {
                    $this->_cacheManager->save(
                        $data,
                        self::APPS_PROXY_PAGE_CACHE_KEY,
                        array(),
                        self::APPS_PROXY_PAGE_CACHE_TTL
                    );
                }

                $this->_curl->close();
            }
        }
        return $data;
    }
}
