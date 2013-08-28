<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_PageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Enterprise_PageCache_Controller_Request extends Magento_Core_Controller_Front_Action
{
    /**
     * Request processing action
     */
    public function processAction()
    {
        /**
         * @var $processor Enterprise_PageCache_Model_Processor
         */
        $processor  = $this->_objectManager->get('Enterprise_PageCache_Model_Processor');

        $content    = Mage::registry('cached_page_content');
        /**
         * @var $containers Enterprise_PageCache_Model_ContainerInterface[]
         */
        $containers = Mage::registry('cached_page_containers');

        $cacheInstance = $this->_objectManager->get('Enterprise_PageCache_Model_Cache');

        foreach ($containers as $container) {
            $container->applyInApp($content);
        }
        $this->getResponse()->appendBody($content);
        // save session cookie lifetime info
        $cacheId = $processor->getSessionInfoCacheId();
        $sessionInfo = $cacheInstance->load($cacheId);
        if ($sessionInfo) {
            $sessionInfo = unserialize($sessionInfo);
        } else {
            $sessionInfo = array();
        }

        /** @var $session Magento_Core_Model_Session */
        $session = $this->_objectManager->get('Magento_Core_Model_Session');
        $cookieName = $session->getSessionName();
        $cookieInfo = array(
            'lifetime' => $session->getCookie()->getLifetime(),
            'path'     => $session->getCookie()->getPath(),
            'domain'   => $session->getCookie()->getDomain(),
            'secure'   => $session->getCookie()->isSecure(),
            'httponly' => $session->getCookie()->getHttponly(),
        );
        if (!isset($sessionInfo[$cookieName]) || $sessionInfo[$cookieName] != $cookieInfo) {
            $sessionInfo[$cookieName] = $cookieInfo;
            // customer cookies have to be refreshed as well as the session cookie
            $sessionInfo[Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER] = $cookieInfo;
            $sessionInfo[Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_GROUP] = $cookieInfo;
            $sessionInfo[Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN] = $cookieInfo;
            $sessionInfo = serialize($sessionInfo);
            $cacheInstance->save($sessionInfo, $cacheId, array(Enterprise_PageCache_Model_Processor::CACHE_TAG));
        }
    }
}
