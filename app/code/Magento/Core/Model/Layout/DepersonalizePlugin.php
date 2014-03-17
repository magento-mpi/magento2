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
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Event\Manager $eventManager
     */
    public function __construct(
        \Magento\App\RequestInterface $request,
        \Magento\Module\Manager $moduleManager,
        \Magento\Event\Manager $eventManager
    ) {
        $this->request = $request;
        $this->moduleManager = $moduleManager;
        $this->eventManager = $eventManager;
    }

    /**
     * After generate Xml
     *
     * @param \Magento\Core\Model\Layout $subject
     * @param \Magento\View\LayoutInterface $result
     * @return \Magento\View\LayoutInterface
     */
    public function afterGenerateXml(\Magento\Core\Model\Layout $subject, $result)
    {
        if ($this->moduleManager->isEnabled('Magento_PageCache')
            && !$this->request->isAjax()
            && $subject->isCacheable()
        ) {
            $this->eventManager->dispatch('depersonalize_clear_session');
            session_write_close();
        }
        return $result;
    }
}
