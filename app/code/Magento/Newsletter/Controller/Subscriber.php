<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Newsletter subscribe controller
 */
namespace Magento\Newsletter\Controller;

use Magento\Framework\App\Action\Context;
use Magento\Framework\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Newsletter\Model\SubscriberFactory;
use Magento\Customer\Model\Url as CustomerUrl;

class Subscriber extends \Magento\Framework\App\Action\Action
{
    /**
     * Customer session
     *
     * @var Session
     */
    protected $_customerSession;

    /**
     * Customer Service
     *
     * @var CustomerAccountServiceInterface
     */
    protected $_customerService;

    /**
     * Subscriber factory
     *
     * @var SubscriberFactory
     */
    protected $_subscriberFactory;

    /**
     * @var \Magento\Framework\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var CustomerUrl
     */
    protected $_customerUrl;

    /**
     * @param Context $context
     * @param SubscriberFactory $subscriberFactory
     * @param CustomerAccountServiceInterface $customerService
     * @param Session $customerSession
     * @param StoreManagerInterface $storeManager
     * @param CustomerUrl $customerUrl
     */
    public function __construct(
        Context $context,
        SubscriberFactory $subscriberFactory,
        CustomerAccountServiceInterface $customerService,
        Session $customerSession,
        StoreManagerInterface $storeManager,
        CustomerUrl $customerUrl
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->_subscriberFactory = $subscriberFactory;
        $this->_customerService = $customerService;
        $this->_customerSession = $customerSession;
        $this->_customerUrl = $customerUrl;
    }
}
