<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Controller;

/**
 * Cms Controller Router
 *
 * @category    Magento
 * @package     Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Router extends \Magento\Framework\App\Router\AbstractRouter
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
     * @var \Magento\Store\Model\StoreManagerInterface
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
     * @var \Magento\Framework\App\State
     */
    protected $_appState;

    /**
     * Url
     *
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Framework\App\ResponseInterface
     */
    protected $_response;

    /**
     * Construct
     *
     * @param \Magento\Framework\App\ActionFactory $actionFactory
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\UrlInterface $url
     * @param \Magento\Framework\App\State $appState
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\App\ResponseInterface $response
     */
    public function __construct(
        \Magento\Framework\App\ActionFactory $actionFactory,
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\UrlInterface $url,
        \Magento\Framework\App\State $appState,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResponseInterface $response
    ) {
        parent::__construct($actionFactory);
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
     * @param \Magento\Framework\App\RequestInterface $request
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function match(\Magento\Framework\App\RequestInterface $request)
    {
        if (!$this->_appState->isInstalled()) {
            $this->_response->setRedirect($this->_url->getUrl('install'))->sendResponse();
            exit;
        }

        $identifier = trim($request->getPathInfo(), '/');

        $condition = new \Magento\Object(array('identifier' => $identifier, 'continue' => true));
        $this->_eventManager->dispatch(
            'cms_controller_router_match_before',
            array('router' => $this, 'condition' => $condition)
        );
        $identifier = $condition->getIdentifier();

        if ($condition->getRedirectUrl()) {
            $this->_response->setRedirect($condition->getRedirectUrl());
            $request->setDispatched(true);
            return $this->_actionFactory->createController(
                'Magento\Framework\App\Action\Redirect',
                array('request' => $request)
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        /** @var \Magento\Cms\Model\Page $page */
        $page = $this->_pageFactory->create();
        $pageId = $page->checkIdentifier($identifier, $this->_storeManager->getStore()->getId());
        if (!$pageId) {
            return null;
        }

        $request->setModuleName('cms')->setControllerName('page')->setActionName('view')->setParam('page_id', $pageId);
        $request->setAlias(\Magento\Url::REWRITE_REQUEST_PATH_ALIAS, $identifier);

        return $this->_actionFactory->createController(
            'Magento\Framework\App\Action\Forward',
            array('request' => $request)
        );
    }
}
