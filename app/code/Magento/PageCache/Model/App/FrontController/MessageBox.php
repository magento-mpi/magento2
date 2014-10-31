<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PageCache\Model\App\FrontController;

use Magento\Framework\App\FrontController;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManager;

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
     * Cookie manager
     *
     * @var \Magento\Framework\Stdlib\CookieManager
     */
    protected $cookieManager;

    /**
     * Cookie metadata factory
     *
     * @var \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory
     */
    protected $cookieMetadataFactory;

    /**
     * Request
     *
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $config;

    /**
     * Message manager
     *
     * @var \Magento\Framework\Message\Manager
     */
    protected $messageManager;

    /**
     * @param CookieManager $cookieManager
     * @param CookieMetadataFactory $cookieMetadataFactory
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        CookieManager $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\Request\Http $request,
        \Magento\PageCache\Model\Config $config,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->cookieManager = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->request = $request;
        $this->config = $config;
        $this->messageManager = $messageManager;
    }

    /**
     * Set Cookie for msg box when it displays first
     *
     * @param FrontController $subject
     * @param \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface $result
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterDispatch(FrontController $subject, $result)
    {
        if ($this->request->isPost() && $this->messageManager->hasMessages()) {
            $publicCookieMetadata = $this->cookieMetadataFactory->createPublicCookieMetadata()
                ->setDuration(self::COOKIE_PERIOD)
                ->setPath('/')
                ->setHttpOnly(false);
            $this->cookieManager->setPublicCookie(self::COOKIE_NAME, 1, $publicCookieMetadata);
        }
        return $result;
    }
}
