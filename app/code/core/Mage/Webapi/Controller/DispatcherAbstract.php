<?php
/**
 * Abstract dispatcher for web API requests.
 *
 * @copyright {}
 */
abstract class Mage_Webapi_Controller_DispatcherAbstract
{
    /** @var Mage_Webapi_Helper_Data */
    protected $_helper;

    /**
     * Initialize dependencies.
     *
     * @param Mage_Webapi_Helper_Data $helper
     */
    public function __construct(Mage_Webapi_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Dispatch request.
     *
     * @return Mage_Webapi_Controller_DispatcherAbstract
     */
    abstract public function dispatch();

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
