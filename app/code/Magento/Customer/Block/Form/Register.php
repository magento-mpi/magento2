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
 * Customer register form block
 */
namespace Magento\Customer\Block\Form;

class Register extends \Magento\Directory\Block\Data
{
    /**
     * Address instance with data
     *
     * @var \Magento\Customer\Model\Address
     */
    protected $_address;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Module\Manager
     */
    protected $_moduleManager;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerHelper;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\App\Cache\Type\Config $configCacheType
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory
     * @param \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory
     * @param \Magento\Module\Manager $moduleManager
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Customer\Model\AddressFactory $addressFactory
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param array $data
     * 
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\App\Cache\Type\Config $configCacheType,
        \Magento\Directory\Model\Resource\Region\CollectionFactory $regionCollFactory,
        \Magento\Directory\Model\Resource\Country\CollectionFactory $countryCollFactory,
        \Magento\Module\Manager $moduleManager,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Customer\Model\AddressFactory $addressFactory,
        \Magento\Customer\Helper\Data $customerHelper,
        array $data = array()
    ) {
        $this->_customerHelper = $customerHelper;
        $this->_moduleManager = $moduleManager;
        $this->_customerSession = $customerSession;
        $this->_addressFactory = $addressFactory;
        parent::__construct(
            $context,
            $coreData,
            $jsonEncoder,
            $configCacheType,
            $regionCollFactory,
            $countryCollFactory,
            $data
        );
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

    protected function _prepareLayout()
    {
        $this->getLayout()->getBlock('head')->setTitle(__('Create New Customer Account'));
        return parent::_prepareLayout();
    }

    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return $this->_customerHelper->getRegisterPostUrl();
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        $url = $this->getData('back_url');
        if (is_null($url)) {
            $url = $this->_customerHelper->getLoginUrl();
        }
        return $url;
    }

    /**
     * Retrieve form data
     *
     * @return \Magento\Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $formData = $this->_customerSession->getCustomerFormData(true);
            $data = new \Magento\Object();
            if ($formData) {
                $data->addData($formData);
                $data->setCustomerData(1);
            }
            if (isset($data['region_id'])) {
                $data['region_id'] = (int)$data['region_id'];
            }
            $this->setData('form_data', $data);
        }
        return $data;
    }

    /**
     * Retrieve customer country identifier
     *
     * @return int
     */
    public function getCountryId()
    {
        $countryId = $this->getFormData()->getCountryId();
        if ($countryId) {
            return $countryId;
        }
        return parent::getCountryId();
    }

    /**
     * Retrieve customer region identifier
     *
     * @return int
     */
    public function getRegion()
    {
        if (false !== ($region = $this->getFormData()->getRegion())) {
            return $region;
        } else if (false !== ($region = $this->getFormData()->getRegionId())) {
            return $region;
        }
        return null;
    }

    /**
     *  Newsletter module availability
     *
     *  @return boolean
     */
    public function isNewsletterEnabled()
    {
        return $this->_moduleManager->isOutputEnabled('Magento_Newsletter');
    }

    /**
     * Return customer address instance
     *
     * @return \Magento\Customer\Model\Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = $this->_createAddress();
        }

        return $this->_address;
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param \Magento\Customer\Model\Form $form
     * @param null $scope
     * @return \Magento\Customer\Block\Form\Register
     */
    public function restoreSessionData(\Magento\Customer\Model\Form $form, $scope = null)
    {
        if ($this->getFormData()->getCustomerData()) {
            $request = $form->prepareRequest($this->getFormData()->getData());
            $data    = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }

    /**
     * @return \Magento\Customer\Model\Address
     */
    protected function _createAddress()
    {
        return $this->_addressFactory->create();
    }
}
