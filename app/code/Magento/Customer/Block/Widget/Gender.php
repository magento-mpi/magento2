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
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Eav\Model\Config $eavConfig
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\Resource\Customer $customerResource
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Eav\Model\Config $eavConfig,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\Resource\Customer $customerResource,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_customerResource = $customerResource;
        parent::__construct($coreData, $context, $eavConfig, $data);
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
        return (bool)$this->_getAttribute('gender')->getIsVisible();
    }

    /**
     * Check if gender attribute marked as required
     *
     * @return bool
     */
    public function isRequired()
    {
        return (bool)$this->_getAttribute('gender')->getIsRequired();
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
