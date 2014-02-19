<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block to render customer's gender attribute
 *
 * @category   Magento
 * @package    Magento_Customer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Customer\Block\Widget;

class Gender extends \Magento\Customer\Block\Widget\AbstractWidget
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Customer\Model\Resource\Customer
     */
    protected $_customerResource;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Customer\Helper\Address $addressHelper
     * @param \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $attributeMetadata
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Resource\Customer $customerResource
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Customer\Helper\Address $addressHelper,
        \Magento\Customer\Service\V1\CustomerMetadataServiceInterface $attributeMetadata,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Resource\Customer $customerResource,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerResource = $customerResource;
        parent::__construct($context, $addressHelper,  $attributeMetadata, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Initialize block
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('widget/gender.phtml');
    }

    /**
     * Check if gender attribute enabled in system
     *
     * @return bool
     */
    public function isEnabled()
    {
        return $this->_getAttribute('gender') ? (bool)$this->_getAttribute('gender')->isVisible() : false;
    }

    /**
     * Check if gender attribute marked as required
     *
     * @return bool
     */
    public function isRequired()
    {
        return $this->_getAttribute('gender') ? (bool)$this->_getAttribute('gender')->isRequired() : false;
    }

    /**
     * Get current customer from session
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        return $this->_customerSession->getCustomer();
    }

    /**
     * Returns options from gender source model
     *
     * @return array
     */
    public function getGenderOptions()
    {
        return $this->_customerResource
            ->getAttribute('gender')
            ->getSource()
            ->getAllOptions();
    }
}
