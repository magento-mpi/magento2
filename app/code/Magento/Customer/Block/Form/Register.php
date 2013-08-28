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
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Customer_Block_Form_Register extends Magento_Directory_Block_Data
{
    /**
     * Address instance with data
     *
     * @var Magento_Customer_Model_Address
     */
    protected $_address;

    /**
     * Customer data
     *
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerData = null;

    /**
     * @param Magento_Customer_Helper_Data $customerData
     * @param  $context
     * @param  $configCacheType
     * @param  $data
     */
    public function __construct(
        Magento_Customer_Helper_Data $customerData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Cache_Type_Config $configCacheType,
        array $data = array()
    ) {
        $this->_customerData = $customerData;
        parent::__construct($context, $configCacheType, $data);
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
        return $this->_customerData->getRegisterPostUrl();
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
            $url = $this->_customerData->getLoginUrl();
        }
        return $url;
    }

    /**
     * Retrieve form data
     *
     * @return Magento_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $formData = Mage::getSingleton('Magento_Customer_Model_Session')->getCustomerFormData(true);
            $data = new Magento_Object();
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
        return $this->_coreData->isModuleOutputEnabled('Magento_Newsletter');
    }

    /**
     * Return customer address instance
     *
     * @return Magento_Customer_Model_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = Mage::getModel('Magento_Customer_Model_Address');
        }

        return $this->_address;
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param Magento_Customer_Model_Form $form
     * @return Magento_Customer_Block_Form_Register
     */
    public function restoreSessionData(Magento_Customer_Model_Form $form, $scope = null)
    {
        if ($this->getFormData()->getCustomerData()) {
            $request = $form->prepareRequest($this->getFormData()->getData());
            $data    = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }
}
