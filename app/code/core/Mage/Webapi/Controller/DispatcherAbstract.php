<?php
/**
 * Abstract dispatcher for web API requests.
 *
 * @copyright {}
 */
abstract class Mage_Webapi_Controller_DispatcherAbstract
{
    /** @var Mage_Webapi_Model_Config */
    protected $_apiConfig;

    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     * @param Mage_Webapi_Model_Config $apiConfig
     */
    public function __construct(
        Mage_Webapi_Helper_Data $helper,
        Mage_Webapi_Model_Config $apiConfig
    ) {
        $this->_helper = $helper;
        $this->_apiConfig = $apiConfig;
    }

    /**
     * Dispatch request.
     *
     * @return Mage_Webapi_Controller_DispatcherAbstract
     */
    abstract public function dispatch();

    /**
     * Retrieve config describing resources available in all APIs.
     * The same resource config must be used in all API types.
     *
     * @return Mage_Webapi_Model_Config
     */
    public function getApiConfig()
    {
        return $this->_apiConfig;
    }

    /**
     * Retrieve Webapi data helper.
     *
     * @return Mage_Webapi_Helper_Data
     */
    public function getHelper()
    {
        return $this->_helper;
    }
}
