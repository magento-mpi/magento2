<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Mage_Backend_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_USE_CUSTOM_ADMIN_URL         = 'default/admin/url/use_custom';
    const XML_PATH_USE_CUSTOM_ADMIN_PATH        = 'default/admin/url/use_custom_path';
    const XML_PATH_CUSTOM_ADMIN_PATH            = 'default/admin/url/custom_path';
    const XML_PATH_BACKEND_AREA_FRONTNAME       = 'default/backend/frontName';
    const BACKEND_AREA_CODE                     = 'adminhtml';

    protected $_pageHelpUrl;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_config;

    /**
     * @var string
     */
    protected $_defaultAreaFrontName;

    protected $_areaFrontName = null;

    /**
     * @var Mage_Core_Model_RouterList
     */
    protected $_routerList;

    /**
     * @param Mage_Core_Model_Config $applicationConfig
     * @param Mage_Core_Helper_Context $context
     * @param Mage_Core_Model_RouterList $routerList
     * @param string $defaultAreaFrontName
     */
    public function __construct(
        Mage_Core_Model_Config $applicationConfig,
        Mage_Core_Helper_Context $context,
        Mage_Core_Model_RouterList $routerList,
        $defaultAreaFrontName
    ) {
        parent::__construct($context);
        $this->_config = $applicationConfig;
        $this->_defaultAreaFrontName = $defaultAreaFrontName;
        $this->_routerList = $routerList;
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
                $router = $this->_routerList->getRouterByFrontName($frontName);

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
        return Mage::getSingleton('Mage_Backend_Model_Url')->getUrl($route, $params);
    }

    public function getCurrentUserId()
    {
        if (Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()) {
            return Mage::getSingleton('Mage_Backend_Model_Auth_Session')->getUser()->getId();
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
        return Mage::helper('Mage_Core_Helper_Data')->uniqHash();
    }

    /**
     * Get backend start page URL
     *
     * @return string
     */
    public function getHomePageUrl()
    {
        return Mage::getSingleton('Mage_Backend_Model_Url')->getRouteUrl('adminhtml');
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
            $customAdminPath = (bool)(string)$this->_config->getNode(self::XML_PATH_USE_CUSTOM_ADMIN_PATH) ?
                (string)$this->_config->getNode(self::XML_PATH_CUSTOM_ADMIN_PATH) :
                '';

            $configAreaFrontName = (string)$this->_config->getNode(self::XML_PATH_BACKEND_AREA_FRONTNAME);

            if ($customAdminPath) {
                $this->_areaFrontName = $customAdminPath;
            } elseif ($configAreaFrontName) {
                $this->_areaFrontName = $configAreaFrontName;
            } else {
                $this->_areaFrontName = $this->_defaultAreaFrontName;
            }
        }

        return $this->_areaFrontName;
    }

    /**
     * Invalidate cache of area front name
     *
     * @return Mage_Backend_Helper_Data
     */
    public function clearAreaFrontName()
    {
        $this->_areaFrontName = null;
        return $this;
    }
}
