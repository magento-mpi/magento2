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

class Request extends \Magento\App\Action\Action
{
    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Stdlib\Cookie
     */
    protected $_cookie;

    /**
     * @param \Magento\App\Action\Context $context
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Stdlib\Cookie $cookie
     */
    public function __construct(
        \Magento\App\Action\Context $context,
        \Magento\Registry $coreRegistry,
        \Magento\Stdlib\Cookie $cookie
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_cookie = $cookie;
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
        /** @var \Magento\Session\Config\ConfigInterface $sessionConfig */
        $sessionConfig = $this->_objectManager->get('Magento\Session\Config\ConfigInterface');
        $cookieName = $session->getName();
        $cookieInfo = array(
            'lifetime' => $sessionConfig->getCookieLifetime(),
            'path'     => $sessionConfig->getCookiePath(),
            'domain'   => $sessionConfig->getCookieDomain(),
            'secure'   => $sessionConfig->getCookieSecure(),
            'httponly' => $sessionConfig->getCookieHttpOnly()
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
