<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RSS Controller with HTTP Basic authentication
 */
namespace Magento\Rss\Controller\Adminhtml;

use Magento\Backend\App\Action;

class Authenticate extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * @param Action\Context $context
     * @param \Magento\Logger $logger
     */
    public function __construct(Action\Context $context, \Magento\Logger $logger)
    {
        $this->_logger = $logger;
        parent::__construct($context);
        $this->_objectManager->get('Magento\Backend\Model\Url')->turnOffSecretKey();
    }

    /**
     * Return required ACL resource for current action
     * @return string
     */
    protected function _getActionAclResource()
    {
        return 'Magento_Rss::rss';
    }

    /**
     * Replace standard admin login form with HTTP Basic authentication
     * @return bool|\Magento\Backend\App\AbstractAction
     */
    protected function _initAuthentication()
    {
        $aclResource = $this->_getActionAclResource();
        if (!$aclResource) {
            return parent::_initAuthentication();
        }

        /** @var $auth \Magento\Backend\Model\Auth */
        $auth = $this->_objectManager->create('Magento\Backend\Model\Auth');
        $session = $auth->getAuthStorage();

        // Try to login using HTTP-authentication
        if (!$session->isLoggedIn()) {
            list($login, $password) = $this->_objectManager->get('Magento\HTTP\Authentication')->getCredentials();
            try {
                $auth->login($login, $password);
            } catch (\Magento\Backend\Model\Auth\Exception $e) {
                $this->_logger->logException($e);
            }
        }

        // Verify if logged in and authorized
        if (!$session->isLoggedIn()
            || !$this->_objectManager->get('Magento\AuthorizationInterface')->isAllowed($aclResource)
        ) {
            $this->_objectManager->get('Magento\HTTP\Authentication')->setAuthenticationFailed('RSS Feeds');
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return true;
    }
}
