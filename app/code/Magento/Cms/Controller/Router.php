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
     * @var Magento_Core_Controller_Response_Http
     */
    protected $_response;

    /**
     * @param Magento_Core_Controller_Response_Http $response
     * @param Magento_Core_Controller_Varien_Action_Factory $controllerFactory
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Magento_Core_Controller_Response_Http $response,
        Magento_Core_Controller_Varien_Action_Factory $controllerFactory,
        Magento_Core_Model_Event_Manager $eventManager
    ) {
        parent::__construct($controllerFactory);
        $this->_response = $response;
        $this->_eventManager = $eventManager;
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
        if (!Mage::isInstalled()) {
            Mage::app()->getFrontController()->getResponse()
                ->setRedirect(Mage::getUrl('install'))
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
            $this->_response->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return $this->_controllerFactory->createController('Magento_Core_Controller_Varien_Action_Redirect',
                array('request' => $request)
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        $page   = Mage::getModel('Magento_Cms_Model_Page');
        $pageId = $page->checkIdentifier($identifier, Mage::app()->getStore()->getId());
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
