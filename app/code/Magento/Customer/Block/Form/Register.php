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
        return $this->helper('Magento\Customer\Helper\Data')->getRegisterPostUrl();
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
            $url = $this->helper('Magento\Customer\Helper\Data')->getLoginUrl();
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
            $formData = \Mage::getSingleton('Magento\Customer\Model\Session')->getCustomerFormData(true);
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
        return $this->_coreData->isModuleOutputEnabled('Magento_Newsletter');
    }

    /**
     * Return customer address instance
     *
     * @return \Magento\Customer\Model\Address
     */
    public function getAddress()
    {
        if (is_null($this->_address)) {
            $this->_address = \Mage::getModel('Magento\Customer\Model\Address');
        }

        return $this->_address;
    }

    /**
     * Restore entity data from session
     * Entity and form code must be defined for the form
     *
     * @param \Magento\Customer\Model\Form $form
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
}
