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
 * Customer address edit block
 */
namespace Magento\Customer\Block\Address;

class Edit extends \Magento\Directory\Block\Data
{
    protected $_address;
    protected $_countryCollection;
    protected $_regionCollection;

    /**
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Model\Cache\Type\Config $configCacheType
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory
     * @param \Magento\Core\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Model\Cache\Type\Config $configCacheType,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory,
        \Magento\Core\Model\Config $config,
        array $data = array()
    ) {
        parent::__construct(
            $configCacheType,
            $coreData,
            $context,
            $storeManager,
            $regionCollFactory,
            $countryCollFactory,
            $data
        );
        $this->_config = $config;
    }


    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->_address = \Mage::getModel('Magento\Customer\Model\Address');

        // Init address object
        if ($id = $this->getRequest()->getParam('id')) {
            $this->_address->load($id);
            if ($this->_address->getCustomerId() != \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerId()) {
                $this->_address->setData(array());
            }
        }

        if (!$this->_address->getId()) {
            $this->_address->setPrefix($this->getCustomer()->getPrefix())
                ->setFirstname($this->getCustomer()->getFirstname())
                ->setMiddlename($this->getCustomer()->getMiddlename())
                ->setLastname($this->getCustomer()->getLastname())
                ->setSuffix($this->getCustomer()->getSuffix());
        }

        if ($headBlock = $this->getLayout()->getBlock('head')) {
            $headBlock->setTitle($this->getTitle());
        }

        if ($postedData = \Mage::getSingleton('Magento\Customer\Model\Session')->getAddressFormData(true)) {
            $this->_address->addData($postedData);
        }
    }

    /**
     * Generate name block html
     *
     * @return string
     */
    public function getNameBlockHtml()
    {
        $nameBlock = $this->getLayout()
            ->createBlock('Magento\Customer\Block\Widget\Name')
            ->setObject($this->getAddress());

        return $nameBlock->toHtml();
    }

    public function getTitle()
    {
        if ($title = $this->getData('title')) {
            return $title;
        }
        if ($this->getAddress()->getId()) {
            $title = __('Edit Address');
        }
        else {
            $title = __('Add New Address');
        }
        return $title;
    }

    public function getBackUrl()
    {
        if ($this->getData('back_url')) {
            return $this->getData('back_url');
        }

        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
    }

    public function getSaveUrl()
    {
        return \Mage::getUrl('customer/address/formPost', array('_secure'=>true, 'id'=>$this->getAddress()->getId()));
    }

    public function getAddress()
    {
        return $this->_address;
    }

    public function getCountryId()
    {
        if ($countryId = $this->getAddress()->getCountryId()) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    public function getRegionId()
    {
        return $this->getAddress()->getRegionId();
    }

    public function getCustomerAddressCount()
    {
        return count(\Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getAddresses());
    }

    public function canSetAsDefaultBilling()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultBilling();
    }

    public function canSetAsDefaultShipping()
    {
        if (!$this->getAddress()->getId()) {
            return $this->getCustomerAddressCount();
        }
        return !$this->isDefaultShipping();;
    }

    public function isDefaultBilling()
    {
        $defaultBilling = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getDefaultBilling();
        return $this->getAddress()->getId() && $this->getAddress()->getId() == $defaultBilling;
    }

    public function isDefaultShipping()
    {
        $defaultShipping = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer()->getDefaultShipping();
        return $this->getAddress()->getId() && $this->getAddress()->getId() == $defaultShipping;
    }

    public function getCustomer()
    {
        return \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomer();
    }

    public function getBackButtonUrl()
    {
        if ($this->getCustomerAddressCount()) {
            return $this->getUrl('customer/address');
        } else {
            return $this->getUrl('customer/account/');
        }
    }

    /**
     * @param string $path
     * @return \Magento\Core\Model\Config\Element
     */
    public function getConfigNode($path)
    {
        return $this->_config->getNode($path);
    }

    /**
     * Get config
     *
     * @param string $path
     * @return mixed
     */
    public function getConfig($path)
    {
        return $this->_storeConfig->getConfig($path);
    }
}
