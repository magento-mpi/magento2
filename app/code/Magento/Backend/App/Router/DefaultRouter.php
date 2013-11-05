<?php
/**
 * Backend router
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 *
 */
namespace Magento\Backend\App\Router;

class DefaultRouter extends \Magento\Core\App\Router\Base
{
    /**
     * List of required request parameters
     * Order sensitive
     * @var array
     */
    protected $_requiredParams = array(
        'areaFrontName',
        'moduleFrontName',
        'controllerName',
        'actionName',
    );

    /**
     * Get router default request path
     * @return string
     */
    protected function _getDefaultPath()
    {
        return (string)$this->_storeConfig->getConfig('web/default/admin', 'admin');
    }

    /**
     * We need to have noroute action in this router
     * not to pass dispatching to next routers
     *
     * @return bool
     */
    protected function _noRouteShouldBeApplied()
    {
        return true;
    }

    /**
     * Check whether URL for corresponding path should use https protocol
     *
     * @param string $path
     * @return bool
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _shouldBeSecure($path)
    {
        return substr((string)$this->_storeConfig->getConfig('web/unsecure/base_url'), 0, 5) === 'https'
            || $this->_storeConfig->getConfigFlag(
                'web/secure/use_in_adminhtml',
                \Magento\Core\Model\AppInterface::ADMIN_STORE_ID
            ) && substr((string)$this->_storeConfig->getConfig('web/secure/base_url'), 0, 5) === 'https';
    }

    /**
     * Retrieve current secure url
     *
     * @param \Magento\App\RequestInterface $request
     * @return string
     */
    protected function _getCurrentSecureUrl($request)
    {
        return $this->_storeManager->getStore(\Magento\Core\Model\AppInterface::ADMIN_STORE_ID)
            ->getBaseUrl('link', true) . ltrim($request->getPathInfo(), '/');
    }

    /**
     * Check whether redirect should be used for secure routes
     *
     * @return bool
     */
    protected function _shouldRedirectToSecure()
    {
        return false;
    }

    /**
     * Build controller class name based on moduleName and controllerName
     *
     * @param string $realModule
     * @param string $controller
     * @return string
     */
    public function getControllerClassName($realModule, $controller)
    {
        /**
         * Start temporary block
         * TODO: Sprint#27. Delete after adminhtml refactoring
         */
        if ($realModule == 'Magento_Adminhtml') {
            return parent::getControllerClassName($realModule, $controller);
        }
        /**
         * End temporary block
         */

        $parts = explode('_', $realModule);
        $parts = array_splice($parts, 0, 2);
        $parts[] = 'Controller';
        $parts[] = \Magento\Backend\Helper\Data::BACKEND_AREA_CODE;
        $parts[] = $controller;

        return \Magento\Core\Helper\String::buildClassName($parts);
    }
}
