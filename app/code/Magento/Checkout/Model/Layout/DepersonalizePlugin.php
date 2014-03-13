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
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Module\Manager $moduleManager,
        \Magento\App\RequestInterface $request
    ) {
        $this->checkoutSession = $checkoutSession;
        $this->moduleManager = $moduleManager;
        $this->request = $request;
    }

    /**
     * After generate Xml
     *
     * @param \Magento\Core\Model\Layout $subject
     * @param $result
     * @return mixed
     */
    public function afterGenerateXml(\Magento\Core\Model\Layout $subject, $result)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache')
            && !$this->request->isAjax()
            && $subject->isCacheable()
        ) {
            $this->checkoutSession->clearStorage();
        }
        return $result;
    }
}
