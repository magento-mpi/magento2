<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Form;

use Magento\Customer\Service\V1\CustomerServiceInterface;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;

/**
 * Customer edit form block
 */
class Edit extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Constructor
     *
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param CustomerServiceInterface $customerService
     * @param CustomerAddressServiceInterface $addressService
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        CustomerServiceInterface $customerService,
        CustomerAddressServiceInterface $addressService,
        array $data = array()
    ) {
        parent::__construct(
            $context, $customerSession, $subscriberFactory, $customerService, $addressService, $data
        );
        $this->_isScopePrivate = true;
    }
}
