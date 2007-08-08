<?php
/**
 * Customer register form block
 *
 * @package     Mage
 * @subpackage  Customer
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Customer_Block_Form_Register extends Mage_Directory_Block_Data
{
    protected function _initChildren()
    {
        $this->getLayout()->getBlock('head')->setTitle(__('Create new customer account'));
        return parent::_initChildren();
    }
    
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return Mage::getUrl('customer/account/createPost', array('_secure'=>true));
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
            $url = Mage::getUrl('customer/account/login');
        }
        return $url;
    }
    
    /**
     * Retrieve form data
     *
     * @return Varien_Object
     */
    public function getFormData()
    {
        $data = $this->getData('form_data');
        if (is_null($data)) {
            $data = new Varien_Object(Mage::getSingleton('customer/session')->getCustomerFormData(true));
            $this->setData('form_data', $data);
        }
        return $data;
    }
    
    public function getCountryId()
    {
        return $this->getFormData()->getCountryId();
    }

    public function getRegionId()
    {
        return $this->getFormData()->getRegion();
    }
}
