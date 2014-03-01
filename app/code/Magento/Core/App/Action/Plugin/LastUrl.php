<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Generic frontend controller
 */
namespace Magento\Core\App\Action\Plugin;

class LastUrl
{
    /**
     * Session namespace to refer in other places
     */
    const SESSION_NAMESPACE = 'frontend';

    /**
     * @var \Magento\Core\Model\Session
     */
    protected $_session;

    /**
     * @var \Magento\UrlInterface
     */
    protected $_url;

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = self::SESSION_NAMESPACE;

    /**
     * @param \Magento\Core\Model\Session $session
     * @param \Magento\UrlInterface $url
     */
    public function __construct(\Magento\Core\Model\Session $session, \Magento\UrlInterface $url)
    {
        $this->_session = $session;
        $this->_url = $url;
    }

    /**
     * Process request
     *
     * @param \Magento\App\Action\Action $subject
     * @param callable $proceed
     * @param \Magento\App\RequestInterface $request
     *
     * @return mixed
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundDispatch(
        \Magento\App\Action\Action $subject,
        \Closure $proceed,
        \Magento\App\RequestInterface $request
    ) {
        $result = $proceed($request);
        $this->_session->setLastUrl($this->_url->getUrl('*/*/*', array('_current' => true)));
        return $result;
    }
}
