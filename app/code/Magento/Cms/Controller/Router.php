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
namespace Magento\Cms\Controller;

class Router extends \Magento\App\Router\AbstractRouter
{
    /**
     * Event manager
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * Store manager
     *
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Page factory
     *
     * @var \Magento\Cms\Model\PageFactory
     */
    protected $_pageFactory;

    /**
     * Config primary
     *
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\App\ResponseInterface
     */
    protected $_response;

    /**
     * Construct
     *
     * @param \Magento\App\ActionFactory $controllerFactory
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Core\Model\UrlInterface $url
     * @param \Magento\App\State $appState
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\App\ActionFactory $controllerFactory,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Core\Model\UrlInterface $url,
        \Magento\App\State $appState,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ResponseInterface $response
    ) {
        parent::__construct($controllerFactory);
        $this->_eventManager = $eventManager;
        $this->_url = $url;
        $this->_appState = $appState;
        $this->_pageFactory = $pageFactory;
        $this->_storeManager = $storeManager;
        $this->_response = $response;
    }

    /**
     * Validate and Match Cms Page and modify request
     *
     * @param \Magento\App\RequestInterface $request
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function match(\Magento\App\RequestInterface $request)
    {
        if (!$this->_appState->isInstalled()) {
            $this->_response->setRedirect($this->_url->getUrl('install'))
                ->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');

        $condition = new \Magento\Object(array(
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
            return $this->_controllerFactory->createController('Magento\Core\Controller\Varien\Action\Redirect',
                array('request' => $request)
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        /** @var \Magento\Cms\Model\Page $page */
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
            \Magento\Core\Model\Url\Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            $identifier
        );

        return $this->_controllerFactory->createController('Magento\Core\Controller\Varien\Action\Forward',
            array('request' => $request)
        );
    }
}
