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
class Magento_Core_Controller_Front_Action extends Magento_Core_Controller_Varien_Action
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
     * @return Magento_Core_Controller_Front_Action
     */
    public function postDispatch()
    {
        parent::postDispatch();
        if (!$this->getFlag('', self::FLAG_NO_START_SESSION )) {
            Mage::getSingleton('Magento_Core_Model_Session')
                ->setLastUrl(Mage::getUrl('*/*/*', array('_current' => true)));
        }
        return $this;
    }

    /**
     * Check if admin is logged in and authorized to access resource by specified ACL path
     *
     * If not authenticated, will try to do it using credentials from HTTP-request
     *
     * @param string $aclResource
     * @return bool
     */
    public function authenticateAndAuthorizeAdmin($aclResource)
    {
        Mage::app()->loadAreaPart(Magento_Core_Model_App_Area::AREA_ADMINHTML,
            Magento_Core_Model_App_Area::PART_CONFIG);

        /** @var $auth Magento_Backend_Model_Auth */
        $auth = Mage::getModel('Magento_Backend_Model_Auth');
        $session = $auth->getAuthStorage();

        // Try to login using HTTP-authentication
        if (!$session->isLoggedIn()) {
            list($login, $password) = $this->_objectManager->get('Magento_Core_Helper_Http')
                ->getHttpAuthCredentials($this->getRequest());
            try {
                $auth->login($login, $password);
            } catch (Magento_Backend_Model_Auth_Exception $e) {
                Mage::logException($e);
            }
        }

        // Verify if logged in and authorized
        if (!$session->isLoggedIn()
            || !Mage::getSingleton('Magento_AuthorizationInterface')->isAllowed($aclResource)) {
            $this->_objectManager->get('Magento_Core_Helper_Http')
                ->failHttpAuthentication($this->getResponse(), 'RSS Feeds');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return false;
        }

        return true;
    }
}
