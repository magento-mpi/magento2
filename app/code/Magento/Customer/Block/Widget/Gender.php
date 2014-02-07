<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Block\Widget;

use Magento\Customer\Service\V1\CustomerMetadataServiceInterface;
use Magento\Customer\Service\V1\CustomerServiceInterface;
use Magento\Customer\Service\V1\Dto\Customer;
use Magento\Customer\Service\V1\Dto\Eav\Option;

/**
 * Block to render customer's gender attribute
 */
class Gender extends AbstractWidget
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var CustomerServiceInterface
     */
    protected $_customerService;

    /**
     * Create an instance of the Gender widget
     *
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param CustomerMetadataServiceInterface $attributeMetadata
     * @param CustomerServiceInterface $customerService
     * @param \Magento\Customer\Model\Session $customerSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        CustomerMetadataServiceInterface $attributeMetadata,
        CustomerServiceInterface $customerService,
        \Magento\Customer\Model\Session $customerSession,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerService = $customerService;
        parent::__construct($context, $addressHelper, $attributeMetadata, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Initialize block
     * @return void
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/gender.phtml');
    }

    /**
     * Check if gender attribute enabled in system
     * @return bool
     */
    public function isEnabled()
    {
        return (bool)$this->_getAttribute('gender')->isVisible();
    }

    /**
     * Check if gender attribute marked as required
     * @return bool
     */
    public function isRequired()
    {
        return (bool)$this->_getAttribute('gender')->isRequired();
    }

    /**
     * Get current customer from session using the customer service
     * @return Customer
     */
    public function getCustomer()
    {
        return $this->_customerService->getCustomer($this->_customerSession->getCustomerId());
    }

    /**
     * Returns options from gender attribute
     * @return Option[]
     */
    public function getGenderOptions()
    {
         return $this->_getAttribute('gender')->getOptions();
    }
}
