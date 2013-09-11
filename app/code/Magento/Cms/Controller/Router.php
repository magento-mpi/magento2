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
     * @param \Magento\Core\Controller\Varien\Action\Factory $controllerFactory
     * @param \Magento\Core\Model\Event\Manager $eventManager
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Factory $controllerFactory,
        \Magento\Core\Model\Event\Manager $eventManager
    ) {
        parent::__construct($controllerFactory);

        $this->_eventManager = $eventManager;
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
        if (!\Mage::isInstalled()) {
            \Mage::app()->getFrontController()->getResponse()
                ->setRedirect(\Mage::getUrl('install'))
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
            \Mage::getSingleton('Magento\Core\Controller\Response\Http')
                ->setRedirect($condition->getRedirectUrl())
                ->sendResponse();
            $request->setDispatched(true);
            return $this->_controllerFactory->createController('\Magento\Core\Controller\Varien\Action\Redirect',
                array('request' => $request)
            );
        }

        if (!$condition->getContinue()) {
            return null;
        }

        $page   = \Mage::getModel('Magento\Cms\Model\Page');
        $pageId = $page->checkIdentifier($identifier, \Mage::app()->getStore()->getId());
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

        return $this->_controllerFactory->createController('\Magento\Core\Controller\Varien\Action\Forward',
            array('request' => $request)
        );
    }
}
