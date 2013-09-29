<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\FullPageCache\Controller;

class Request extends \Magento\Core\Controller\Front\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context);
    }

    /**
     * Request processing action
     */
    public function processAction()
    {
        /**
         * @var $processor \Magento\FullPageCache\Model\Processor
         */
        $processor  = $this->_objectManager->get('Magento\FullPageCache\Model\Processor');

        $content    = $this->_coreRegistry->registry('cached_page_content');
        /**
         * @var $containers \Magento\FullPageCache\Model\ContainerInterface[]
         */
        $containers = $this->_coreRegistry->registry('cached_page_containers');

        $cacheInstance = $this->_objectManager->get('Magento\FullPageCache\Model\Cache');

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

        /** @var $session \Magento\Core\Model\Session */
        $session = $this->_objectManager->get('Magento\Core\Model\Session');
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
            $sessionInfo[\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER] = $cookieInfo;
            $sessionInfo[\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER_GROUP] = $cookieInfo;
            $sessionInfo[\Magento\FullPageCache\Model\Cookie::COOKIE_CUSTOMER_LOGGED_IN] = $cookieInfo;
            $sessionInfo = serialize($sessionInfo);
            $cacheInstance->save($sessionInfo, $cacheId, array(\Magento\FullPageCache\Model\Processor::CACHE_TAG));
        }
    }
}
