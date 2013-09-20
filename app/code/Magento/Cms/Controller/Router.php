<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Cms Controller Router
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cms_Controller_Router extends Magento_Core_Controller_Varien_Router_Abstract
{
    /**
     * Event manager
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var Magento_Cms_Model_PageFactory
     */
    protected $_pageFactory;

    /**
     * Config primary
     *
     * @var Magento_Core_Model_Config_Primary
     */
    protected $_configPrimary;

    /**
     * Url
     *
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_url;

    /**
     * Response
     *
     * @var Magento_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * Construct
     *
     * @param Magento_Core_Controller_Varien_Action_Factory $controllerFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_UrlInterface $url
     * @param Magento_Core_Model_Config_Primary $configPrimary
     * @param Magento_Cms_Model_PageFactory $pageFactory
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Controller_Response_Http $response
     */
    public function __construct(
        Magento_Core_Controller_Varien_Action_Factory $controllerFactory,
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_UrlInterface $url,
        Magento_Core_Model_Config_Primary $configPrimary,
        Magento_Cms_Model_PageFactory $pageFactory,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Controller_Response_Http $response
    ) {
        parent::__construct($controllerFactory);
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_configPrimary = $configPrimary;
        $this->_pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function match(Magento_Core_Controller_Request_Http $request)
    {
        if (!$this->_configPrimary->getInstallDate()) {
            $this->_response
                ->setRedirect($this->_url->getUrl('install'))
                ->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');

        $condition = new Magento_Object(array(
            'identifier' => $identifier,
            'continue'   => true
        ));
        $this->_eventManager->dispatch('cms_controller_router_match_before', array(
            'router'    => $this,
            'condition' => $condition
        ));
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            Mage::getSingleton('Magento_Core_Controller_Response_Http')
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return $this->_controllerFactory->createController('Magento_Core_Controller_Varien_Action_Redirect',
                array('request' => $request)
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        /** @var Magento_Cms_Model_Page $page */
        $page   = $this->_pageFactory->create();
        $pageId = $page->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
        if (!$pageId) {
            return null;
        }

        $request->setModuleName('cms')
            ->setControllerName('page')
            ->setActionName('view')
            ->setParam('page_id', $pageId);
        $request->setAlias(
            Magento_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );

        return $this->_controllerFactory->createController('Magento_Core_Controller_Varien_Action_Forward',
            array('request' => $request)
        );
    }
}
