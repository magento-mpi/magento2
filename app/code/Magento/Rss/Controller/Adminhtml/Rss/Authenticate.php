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
namespace Magento\Rss\Controller\Adminhtml\Rss;

class Authenticate extends \Magento\Adminhtml\Controller\Action
{
    /**
     * @var \Magento\Core\Model\Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Backend\Controller\Context $context
     */
    public function __construct(\Magento\Backend\Controller\Context $context)
    {
        $this->_logger = $context->getLogger();
        parent::__construct($context);
        $this->_objectManager->get('Magento\Backend\Model\Url')->turnOffSecretKey();
    }

    protected function _getActionAclResource()
    {
        return 'Magento_Rss::rss';
    }

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
            list($login, $password) = $this->_objectManager->get('Magento\Core\Helper\Http')
                ->getHttpAuthCredentials($this->getRequest());
            try {
                $auth->login($login, $password);
            } catch (\Magento\Backend\Model\Auth\Exception $e) {
                $this->_logger->logException($e);
            }
        }

        // Verify if logged in and authorized
        if (!$session->isLoggedIn()
            || !$this->_objectManager->get('Magento\AuthorizationInterface')->isAllowed($aclResource)) {
            $this->_objectManager->get('Magento\Core\Helper\Http')
                ->failHttpAuthentication($this->getResponse(), 'RSS Feeds');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return true;
    }
}
