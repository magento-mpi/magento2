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
     * @var \Magento\View\Element\Messages
     */
    protected $messageManager;

    /**
     * @param \Magento\Stdlib\Cookie $cookie
     * @param \Magento\App\Request\Http $request
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\View\Element\Messages $messageManager
     */
    public function __construct(
        \Magento\Stdlib\Cookie $cookie,
        \Magento\App\Request\Http $request,
        \Magento\PageCache\Model\Config $config,
        \Magento\View\Element\Messages $messageManager
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
        if ($this->request->isPost() && $this->config->isEnabled() && $this->isMessage()) {
            $this->cookie->set(self::COOKIE_NAME, 1, self::COOKIE_PERIOD, '/');
        }
        return $response;
    }

    /**
     * Return true if there any messages for customer
     * false - another case
     *
     * @return bool
     */
    protected function isMessage()
    {
        return $this->messageManager->getMessageCollection() ? true : false;
    }
}
