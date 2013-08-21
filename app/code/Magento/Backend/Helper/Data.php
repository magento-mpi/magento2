<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Helper_Data extends Magento_Core_Helper_Abstract
{
    const XML_PATH_BACKEND_FRONTNAME            = 'global/areas/adminhtml/frontName';
    const XML_PATH_USE_CUSTOM_ADMIN_URL         = 'default/admin/url/use_custom';
    const XML_PATH_USE_CUSTOM_ADMIN_PATH        = 'default/admin/url/use_custom_path';
    const XML_PATH_CUSTOM_ADMIN_PATH            = 'default/admin/url/custom_path';
    const BACKEND_AREA_CODE                     = 'adminhtml';

    protected $_pageHelpUrl;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_config;

    protected $_areaFrontName = null;

    /**
     * @param Magento_Core_Model_Config $applicationConfig
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(Magento_Core_Model_Config $applicationConfig, Magento_Core_Helper_Context $context)
    {
        parent::__construct($context);
        $this->_config = $applicationConfig;
    }

    public function getPageHelpUrl()
    {
        if (!$this->_pageHelpUrl) {
            $this->setPageHelpUrl();
        }
        return $this->_pageHelpUrl;
    }

    public function setPageHelpUrl($url=null)
    {
        if (is_null($url)) {
            $request = Mage::app()->getRequest();
            $frontModule = $request->getControllerModule();
            if (!$frontModule) {
                $frontName = $request->getModuleName();
                $router = Mage::app()->getFrontController()->getRouterByFrontName($frontName);

                $frontModule = $router->getModulesByFrontName($frontName);
                if (empty($frontModule) === false) {
                    $frontModule = $frontModule[0];
                } else {
                    $frontModule = null;
                }
            }
            $url = 'http://www.magentocommerce.com/gethelp/';
            $url.= Mage::app()->getLocale()->getLocaleCode().'/';
            $url.= $frontModule.'/';
            $url.= $request->getControllerName().'/';
            $url.= $request->getActionName().'/';

            $this->_pageHelpUrl = $url;
        }
        $this->_pageHelpUrl = $url;

        return $this;
    }

    public function addPageHelpUrl($suffix)
    {
        $this->_pageHelpUrl = $this->getPageHelpUrl().$suffix;
        return $this;
    }

    public function getUrl($route='', $params=array())
    {
        return Mage::getSingleton('Magento_Backend_Model_Url')->getUrl($route, $params);
    }

    public function getCurrentUserId()
    {
        if (Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()) {
            return Mage::getSingleton('Magento_Backend_Model_Auth_Session')->getUser()->getId();
        }
        return false;
    }

    /**
     * Decode filter string
     *
     * @param string $filterString
     * @return array
     */
    public function prepareFilterString($filterString)
    {
        $data = array();
        $filterString = base64_decode($filterString);
        parse_str($filterString, $data);
        array_walk_recursive($data, array($this, 'decodeFilter'));
        return $data;
    }

    /**
     * Decode URL encoded filter value recursive callback method
     *
     * @param string $value
     */
    public function decodeFilter(&$value)
    {
        $value = rawurldecode($value);
    }

    /**
     * Generate unique token for reset password confirmation link
     *
     * @return string
     */
    public function generateResetPasswordLinkToken()
    {
        return Mage::helper('Magento_Core_Helper_Data')->uniqHash();
    }

    /**
     * Get backend start page URL
     *
     * @return string
     */
    public function getHomePageUrl()
    {
        return Mage::getSingleton('Magento_Backend_Model_Url')->getRouteUrl('adminhtml');
    }

    /**
     * Return Backend area code
     *
     * @return string
     */
    public function getAreaCode()
    {
        return self::BACKEND_AREA_CODE;
    }

    /**
     * Return Backend area front name
     *
     * @return string
     */
    public function getAreaFrontName()
    {
        if (null === $this->_areaFrontName) {
            $this->_areaFrontName = (bool)(string)$this->_config->getNode(self::XML_PATH_USE_CUSTOM_ADMIN_PATH) ?
                (string)$this->_config->getNode(self::XML_PATH_CUSTOM_ADMIN_PATH) :
                (string)$this->_config->getNode(self::XML_PATH_BACKEND_FRONTNAME);
        }
        return $this->_areaFrontName;
    }

    /**
     * Invalidate cache of area front name
     *
     * @return Magento_Backend_Helper_Data
     */
    public function clearAreaFrontName()
    {
        $this->_areaFrontName = null;
        return $this;
    }
}
