<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\WebsiteRestriction\Model\Observer;

class RestrictWebsite
{
    /**
     * @var \Magento\WebsiteRestriction\Model\ConfigInterface
     */
    protected $config;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $eventManager;

    /**
     * Website Restrictor
     *
     * @var \Magento\WebsiteRestriction\Model\Restrictor
     */
    protected $restrictor;

    /**
     * @param \Magento\WebsiteRestriction\Model\ConfigInterface $config
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\WebsiteRestriction\Model\Restrictor $restrictor
     */
    public function __construct(
        \Magento\WebsiteRestriction\Model\ConfigInterface $config,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\WebsiteRestriction\Model\Restrictor $restrictor
    ) {
        $this->config = $config;
        $this->eventManager = $eventManager;
        $this->restrictor = $restrictor;
    }

    /**
     * Implement website stub or private sales restriction
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function execute($observer)
    {
        /* @var $controller \Magento\Framework\App\Action\Action */
        $controller = $observer->getEvent()->getControllerAction();

        $dispatchResult = new \Magento\Framework\Object(array('should_proceed' => true, 'customer_logged_in' => false));
        $this->eventManager->dispatch(
            'websiterestriction_frontend',
            array('controller' => $controller, 'result' => $dispatchResult)
        );
        if (!$dispatchResult->getShouldProceed()) {
            return;
        }
        if (!$this->config->isRestrictionEnabled()) {
            return;
        }
        $this->restrictor->restrict(
            $observer->getEvent()->getRequest(),
            $controller->getResponse(),
            $dispatchResult->getCustomerLoggedIn()
        );
    }
}
