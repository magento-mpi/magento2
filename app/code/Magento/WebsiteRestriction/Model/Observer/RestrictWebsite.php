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
     * @var \Magento\Framework\Object\Factory
     */
    protected $objectFactory;

    /**
     * @param \Magento\WebsiteRestriction\Model\ConfigInterface $config
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\WebsiteRestriction\Model\Restrictor $restrictor
     * @param \Magento\Framework\Object\Factory $objectFactory
     */
    public function __construct(
        \Magento\WebsiteRestriction\Model\ConfigInterface $config,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\WebsiteRestriction\Model\Restrictor $restrictor,
        \Magento\Framework\Object\Factory $objectFactory
    ) {
        $this->config = $config;
        $this->eventManager = $eventManager;
        $this->restrictor = $restrictor;
        $this->objectFactory = $objectFactory;
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

        $dispatchResult = $this->objectFactory->create(array('should_proceed' => true, 'customer_logged_in' => false));
        $this->eventManager->dispatch(
            'websiterestriction_frontend',
            array('controller' => $controller, 'result' => $dispatchResult)
        );

        if (!$dispatchResult->getShouldProceed() || !$this->config->isRestrictionEnabled()) {
            return;
        }

        $this->restrictor->restrict(
            $observer->getEvent()->getRequest(),
            $controller->getResponse(),
            $dispatchResult->getCustomerLoggedIn()
        );
    }
}
