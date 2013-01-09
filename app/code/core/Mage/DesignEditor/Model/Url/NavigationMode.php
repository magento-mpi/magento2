<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Navigation mode design editor url model
 */
class Mage_DesignEditor_Model_Url_NavigationMode extends Mage_Core_Model_Url
{
    /**
     * VDE helper
     *
     * @var Mage_DesignEditor_Helper_Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param Mage_DesignEditor_Helper_Data $helper
     * @param array $data
     */
    public function __construct(Mage_DesignEditor_Helper_Data $helper, array $data = array())
    {
        $this->_helper = $helper;
        parent::__construct($data);
    }

    /**
     * Retrieve route URL
     *
     * @param string $routePath
     * @param array $routeParams
     * @return string
     */
    public function getRouteUrl($routePath = null, $routeParams = null)
    {
        $url = parent::getRouteUrl($routePath, $routeParams);
        $baseUrl = trim($this->getBaseUrl(), '/');
        $vdeBaseUrl = $baseUrl . '/' . $this->_helper->getFrontName();
        if (strpos($url, $baseUrl) === 0 && strpos($url, $vdeBaseUrl) === false) {
            $url = str_replace($baseUrl, $vdeBaseUrl, $url);
        }
        return $url;
    }
}
