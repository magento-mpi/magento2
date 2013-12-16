<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Controller;

class Index extends \Magento\App\Action\Action
{
    public function indexAction()
    {

    }

    /**
     * 404 not found action
     */
    public function notFoundAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1', '404 Not Found');
        $this->getResponse()->setHttpResponseCode(404);
        $this->getResponse()->setBody(__('Requested resource not found'));
    }

    /**
     * No cookies action
     */
    public function noCookiesAction()
    {
        $redirect = new \Magento\Object();
        $this->_eventManager->dispatch('controller_action_nocookies', array(
            'action' => $this,
            'redirect' => $redirect
        ));

        $url = $redirect->getRedirectUrl();
        if ($url) {
            $this->getResponse()->setRedirect($url);
        } elseif ($redirect->getRedirect()) {
            $this->_redirect($redirect->getPath(), $redirect->getArguments());
        } else {
            $this->_view->loadLayout(array('default', 'noCookie'));
            $this->_view->renderLayout();
        }

        $this->getRequest()->setDispatched(true);
    }
}
