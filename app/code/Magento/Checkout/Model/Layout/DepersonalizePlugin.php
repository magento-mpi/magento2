<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Model\Layout;

/**
 * Class DepersonalizePlugin
 */
class DepersonalizePlugin
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $checkoutSession;

    /**
     * @var \Magento\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $cacheConfig;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\PageCache\Model\Config $cacheConfig
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Module\Manager $moduleManager,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\PageCache\Model\Config $cacheConfig
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->moduleManager = $moduleManager;
        $this->request = $request;
        $this->cacheConfig = $cacheConfig;
    }

    /**
     * After generate Xml
     *
     * @param \Magento\Framework\View\LayoutInterface $subject
     * @param \Magento\Framework\View\LayoutInterface $result
     * @return \Magento\Framework\View\LayoutInterface
     */
    public function afterGenerateXml(\Magento\Framework\View\LayoutInterface $subject, $result)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache')
            && $this->cacheConfig->isEnabled()
            && !$this->request->isAjax()
            && $subject->isCacheable()
        ) {
            $this->checkoutSession->clearStorage();
        }
        return $result;
    }
}
