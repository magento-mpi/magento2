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

class Router extends \Magento\Core\Controller\Varien\Router\AbstractRouter
{
    /**
     * Event manager
     *
     * @var \Magento\Core\Model\Event\Manager
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
     * @var \Magento\Core\Model\Config\Primary
     */
    protected $_configPrimary;

    /**
     * Url
     *
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_url;

    /**
     * Response
     *
     * @var \Magento\Core\Controller\Response\Http
     */
    protected $_response;

    /**
     * Construct
     *
     * @param \Magento\Core\Controller\Varien\Action\Factory $controllerFactory
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\UrlInterface $url
     * @param \Magento\Core\Model\Config\Primary $configPrimary
     * @param \Magento\Cms\Model\PageFactory $pageFactory
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Controller\Response\Http $response
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Factory $controllerFactory,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\UrlInterface $url,
        \Magento\Core\Model\Config\Primary $configPrimary,
        \Magento\Cms\Model\PageFactory $pageFactory,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Controller\Response\Http $response
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
     * @param \Magento\Core\Controller\Request\Http $request
     * @return bool
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function match(\Magento\Core\Controller\Request\Http $request)
    {
        if (!$this->_configPrimary->getInstallDate()) {
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
