<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic frontend controller
 */
namespace Magento\Core\Controller\Front;

class Action extends \Magento\Core\Controller\Varien\Action
{
    /**
     * Session namespace to refer in other places
     */
    const SESSION_NAMESPACE = 'frontend';

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = self::SESSION_NAMESPACE;

    /**
     * Remember the last visited url in the session
     *
     * @return \Magento\Core\Controller\Front\Action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if (!$this->getFlag('', self::FLAG_NO_START_SESSION )) {
            \Mage::getSingleton('Magento\Core\Model\Session')
                ->setLastUrl(\Mage::getUrl('*/*/*', array('_current' => true)));
        }
        return $this;
    }

    /**
     * Check if admin is logged in and authorized to access resource by specified ACL path
     *
     * If not authenticated, will try to do it using credentials from HTTP-request
     *
     * @param string $aclResource
     * @param Magento_Core_Model_Logger $logger
     * @return bool
     */
    public function authenticateAndAuthorizeAdmin($aclResource, $logger)
    {
        \Mage::app()->loadAreaPart(\Magento\Core\Model\App\Area::AREA_ADMINHTML,
            \Magento\Core\Model\App\Area::PART_CONFIG);

        /** @var $auth \Magento\Backend\Model\Auth */
        $auth = \Mage::getModel('Magento\Backend\Model\Auth');
        $session = $auth->getAuthStorage();

        // Try to login using HTTP-authentication
        if (!$session->isLoggedIn()) {
            list($login, $password) = $this->_objectManager->get('Magento\Core\Helper\Http')
                ->getHttpAuthCredentials($this->getRequest());
            try {
                $auth->login($login, $password);
            } catch (Magento_Backend_Model_Auth_Exception $e) {
                $logger->logException($e);
            }
        }

        // Verify if logged in and authorized
        if (!$session->isLoggedIn()
            || !\Mage::getSingleton('Magento\AuthorizationInterface')->isAllowed($aclResource)) {
            $this->_objectManager->get('Magento\Core\Helper\Http')
                ->failHttpAuthentication($this->getResponse(), 'RSS Feeds');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return true;
    }
}
