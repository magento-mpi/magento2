<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\DesignEditor\Controller\Varien\Router;

class Standard extends \Magento\Core\Controller\Varien\Router\Base
{
    /**
     * @var \Magento\ObjectManager
     */
    protected $_objectManager;

    /**
     * Routers that must not been matched
     *
     * @var array
     */
    protected $_excludedRouters = array('admin', 'vde');

    /**
     * @param \Magento\Core\Controller\Varien\Action\Factory $controllerFactory
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\Filesystem $filesystem
     * @param \Magento\Core\Model\App $app
     * @param \Magento\Core\Model\Config\Scope $configScope
     * @param \Magento\Core\Model\Route\Config $routeConfig
     * @param string $areaCode
     * @param string $baseController
     * @param string $routerId
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Factory $controllerFactory,
        \Magento\ObjectManager $objectManager,
        \Magento\Filesystem $filesystem,
        \Magento\Core\Model\App $app,
        \Magento\Core\Model\Config\Scope $configScope,
        \Magento\Core\Model\Route\Config $routeConfig,
        $areaCode,
        $baseController,
        $routerId
    ) {
        parent::__construct($controllerFactory, $filesystem, $app, $configScope, $routeConfig, $areaCode,
            $baseController, $routerId);
        $this->_objectManager = $objectManager;
    }

    /**
     * Match provided request and if matched - return corresponding controller
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @return \Magento\Core\Controller\Front\Action|null
     */
    public function match(\Magento\Core\Controller\Request\Http $request)
    {
        // if URL has VDE prefix
        if (!$this->_objectManager->get('Magento\DesignEditor\Helper\Data')->isVdeRequest($request)) {
            return null;
        }

        // user must be logged in admin area
        if (!$this->_objectManager->get('Magento\Backend\Model\Auth\Session')->isLoggedIn()) {
            return null;
        }

        // override VDE configuration
        $this->_overrideConfiguration();

        // prepare request to imitate
        $this->_prepareVdeRequest($request);

        // apply rewrites
        $this->getFront()->applyRewrites($request);

        // match routers
        $controller = null;
        $routers = $this->_getMatchedRouters();
        /** @var $router \Magento\Core\Controller\Varien\Router\AbstractRouter */
        foreach ($routers as $router) {
            /** @var $controller \Magento\Core\Controller\Varien\ActionAbstract */
            $controller = $router->match($request);
            if ($controller) {
                $this->_objectManager->get('Magento\DesignEditor\Model\State')
                    ->update($this->_areaCode, $request, $controller);
                break;
            }
        }

        // set inline translation mode
        $this->_objectManager->get('Magento\DesignEditor\Helper\Data')->setTranslationMode($request);

        return $controller;
    }

    /**
     * Modify request path to imitate basic request
     *
     * @param \Magento\Core\Controller\Request\Http $request
     * @return \Magento\DesignEditor\Controller\Varien\Router\Standard
     */
    protected function _prepareVdeRequest(\Magento\Core\Controller\Request\Http $request)
    {
        list($vdeFrontName, $designMode, $themeId) = explode('/', trim($request->getPathInfo(), '/'));
        $request->setAlias('editorMode', $designMode);
        $request->setAlias('themeId', (int)$themeId);
        $vdePath = implode('/', array($vdeFrontName, $designMode, $themeId));
        $noVdePath = substr($request->getPathInfo(), strlen($vdePath) + 1) ?: '/';
        $request->setPathInfo($noVdePath);
        return $this;
    }

    /**
     * Returns list of routers that must been tried to match
     *
     * @return array
     */
    protected function _getMatchedRouters()
    {
        $routers = $this->getFront()->getRouters();
        foreach (array_keys($routers) as $name) {
            if (in_array($name, $this->_excludedRouters)) {
                unset($routers[$name]);
            }
        }
        return $routers;
    }

    /**
     * Override frontend configuration with VDE area data
     */
    protected function _overrideConfiguration()
    {
        $vdeNode = $this->_objectManager->get('Magento\Core\Model\Config')
            ->getNode(\Magento\DesignEditor\Model\Area::AREA_VDE);
        if ($vdeNode) {
            $this->_objectManager->get('Magento\Core\Model\Config')->
                getNode(\Magento\Core\Model\App\Area::AREA_FRONTEND)
                ->extend($vdeNode, true);
        }
    }
}
