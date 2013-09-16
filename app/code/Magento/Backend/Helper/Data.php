<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @SuppressWarnings(PHPMD.LongVariable)
 */
namespace Magento\Backend\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    const XML_PATH_USE_CUSTOM_ADMIN_URL         = 'admin/url/use_custom';
    const XML_PATH_USE_CUSTOM_ADMIN_PATH        = 'admin/url/use_custom_path';
    const XML_PATH_CUSTOM_ADMIN_PATH            = 'admin/url/custom_path';
    const XML_PATH_BACKEND_AREA_FRONTNAME       = 'default/backend/frontName';
    const BACKEND_AREA_CODE                     = 'adminhtml';

    protected $_pageHelpUrl;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\Config\Primary
     */
    protected $_primaryConfig;

    /**
     * @var string
     */
    protected $_dfltAreaFrontName;

    /**
     * Area front name
     * @var string
     */
    protected $_areaFrontName = null;

    /**
     * @var \Magento\Core\Model\RouterList
     */
    protected $_routerList;

    /**
     * @param \Magento\Core\Model\Config $applicationConfig
     * @param \Magento\Core\Model\Config\Primary $primaryConfig
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\RouterList $routerList
     * @param string $dfltAreaFrontName
     */
    public function __construct(
        \Magento\Core\Model\Config $applicationConfig,
        \Magento\Core\Model\Config\Primary $primaryConfig,
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\RouterList $routerList,
        $dfltAreaFrontName
    ) {
        parent::__construct($context);
        $this->_config = $applicationConfig;
        $this->_primaryConfig = $primaryConfig;
        $this->_dfltAreaFrontName = $dfltAreaFrontName;
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
            $request = \Mage::app()->getRequest();
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
            $url.= \Mage::app()->getLocale()->getLocaleCode().'/';
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
        return \Mage::getSingleton('Magento\Backend\Model\Url')->getUrl($route, $params);
    }

    public function getCurrentUserId()
    {
        if (\Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()) {
            return \Mage::getSingleton('Magento\Backend\Model\Auth\Session')->getUser()->getId();
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
        return \Mage::helper('Magento\Core\Helper\Data')->uniqHash();
    }

    /**
     * Get backend start page URL
     *
     * @return string
     */
    public function getHomePageUrl()
    {
        return \Mage::getSingleton('Magento\Backend\Model\Url')->getRouteUrl('adminhtml');
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
            $isCustomPathUsed = (bool)(string)$this->_config->getValue(self::XML_PATH_USE_CUSTOM_ADMIN_PATH, 'default');
            $configAreaFrontName = (string)$this->_primaryConfig->getNode(self::XML_PATH_BACKEND_AREA_FRONTNAME);

            if ($isCustomPathUsed) {
                $this->_areaFrontName = (string)$this->_config->getValue(self::XML_PATH_CUSTOM_ADMIN_PATH, 'default');
            } elseif ($configAreaFrontName) {
                $this->_areaFrontName = $configAreaFrontName;
            } else {
                $this->_areaFrontName = $this->_dfltAreaFrontName;
            }
        }

        return $this->_areaFrontName;
    }

    /**
     * Invalidate cache of area front name
     *
     * @return \Magento\Backend\Helper\Data
     */
    public function clearAreaFrontName()
    {
        $this->_areaFrontName = null;
        return $this;
    }
}
