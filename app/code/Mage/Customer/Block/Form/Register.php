<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Customer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer register form block
 */
class Mage_Customer_Block_Form_Register extends Mage_Directory_Block_Data
{
    /**
     * Address instance with data
     *
     * @var Mage_Customer_Model_Address
     */
    protected $_address;

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
        return $this->helper('Mage_Customer_Helper_Data')->getRegisterPostUrl();
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
            $url = $this->helper('Mage_Customer_Helper_Data')->getLoginUrl();
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
            $formData = Mage::getSingleton('Mage_Customer_Model_Session')->getCustomerFormData(true);
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
        return Mage::helper('Mage_Core_Helper_Data')->isModuleOutputEnabled('Mage_Newsletter');
    }

    /**
     * Return customer address instance
     *
     * @return Mage_Customer_Model_Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = Mage::getModel('Mage_Customer_Model_Address');
        }

        return $this->_address;
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param Mage_Customer_Model_Form $form
     * @return Mage_Customer_Block_Form_Register
     */
    public function restoreSessionData(Mage_Customer_Model_Form $form, $scope = null)
    {
        if ($this->getFormData()->getCustomerData()) {
            $request = $form->prepareRequest($this->getFormData()->getData());
            $data    = $form->extractData($request, $scope, false);
            $form->restoreData($data);
        }

        return $this;
    }
}
