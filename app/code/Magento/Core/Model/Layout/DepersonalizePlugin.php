<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Layout;

/**
 * Class DepersonalizePlugin
 */
class DepersonalizePlugin
{
    /**
     * @var \Magento\App\RequestInterface
     */
    protected $request;

    /**
     * @var \Magento\Module\Manager
     */
    protected $moduleManager;

    /**
     * @var \Magento\Event\Manager
     */
    protected $eventManager;

    /**
     * @var \Magento\PageCache\Model\Config
     */
    protected $cacheConfig;

    /**
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Event\Manager $eventManager
     * @param \Magento\PageCache\Model\Config $cacheConfig
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Module\Manager $moduleManager,
        \Magento\Event\Manager $eventManager,
        \Magento\PageCache\Model\Config $cacheConfig
    ) {
        $this->request = $request;
        $this->moduleManager = $moduleManager;
        $this->eventManager = $eventManager;
        $this->cacheConfig = $cacheConfig;
    }

    /**
     * After generate Xml
     *
     * @param \Magento\View\LayoutInterface $subject
     * @param \Magento\View\LayoutInterface $result
     * @return \Magento\View\LayoutInterface
     */
    public function afterGenerateXml(\Magento\View\LayoutInterface $subject, $result)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache')
            && $this->cacheConfig->isEnabled()
            && !$this->request->isAjax()
            && $subject->isCacheable()
        ) {
            $this->eventManager->dispatch('depersonalize_clear_session');
            session_write_close();
        }
        return $result;
    }
}
