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

/**
 * Class MsgBox
 */
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
     * @var \Magento\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magento\App\ConfigInterface
     */
    protected $config;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @param \Magento\Stdlib\Cookie $cookie
     * @param \Magento\App\Request\Http $request
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Stdlib\Cookie $cookie,
        \Magento\App\Request\Http $request,
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
     * @param \Magento\App\FrontController $subject
     * @param \Magento\App\ResponseInterface $response
     *
     * @return \Magento\App\ResponseInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(\Magento\App\FrontController $subject, \Magento\App\ResponseInterface $response)
    {
        if ($this->request->isPost() && $this->config->isEnabled() && $this->hasMessages()) {
            $this->cookie->set(self::COOKIE_NAME, 1, self::COOKIE_PERIOD, '/');
        }
        return $response;
    }

    /**
     * Returns true if there are any messages for customer,
     * false - in other case
     *
     * @return bool
     */
    protected function hasMessages()
    {
        return ($this->messageManager->getMessages()->getCount() > 0);
    }
}
