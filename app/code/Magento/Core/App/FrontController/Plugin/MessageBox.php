<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\App\FrontController\Plugin;

class MessageBox
{
    /**
     * Name of cookie that holds private content version
     */
    const COOKIE_NAME = 'message_box_display';

    /**
     * Ten years cookie period
     */
    const COOKIE_PERIOD = 315360000;

    /**
     * Cookie
     *
     * @var \Magento\Stdlib\Cookie
     */
    protected $cookie;

    /**
     * Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Message\Manager
     */
    protected $messageManager;

    /**
     * @param \Magento\Stdlib\Cookie $cookie
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Stdlib\Cookie $cookie,
        \Magento\Framework\App\Request\Http $request,
        \Magento\PageCache\Model\Config $config,
        \Magento\Message\ManagerInterface $messageManager
    ) {
        $this->cookie = $cookie;
        $this->request = $request;
        $this->config = $config;
        $this->messageManager = $messageManager;
    }

    /**
     * Set Cookie for msg box when it displays first
     *
     * @param \Magento\Framework\App\FrontController $subject
     * @param \Magento\Framework\App\ResponseInterface $response
     *
     * @return \Magento\Framework\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(\Magento\Framework\App\FrontController $subject, \Magento\Framework\App\ResponseInterface $response)
    {
        if ($this->request->isPost() && $this->messageManager->hasMessages()) {
            $this->cookie->set(self::COOKIE_NAME, 1, self::COOKIE_PERIOD, '/');
        }
        return $response;
    }
}
