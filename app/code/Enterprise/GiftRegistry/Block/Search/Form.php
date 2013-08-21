<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_GiftRegistry
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift registry search form
 *
 * @category   Enterprise
 * @package    Enterprise_GiftRegistry
 */
class Enterprise_GiftRegistry_Block_Search_Form extends Magento_Core_Block_Template
{
    protected $_formData = null;

    /**
     * Retrieve form header
     *
     * @return string
     */
    public function getFormHeader()
    {
        return __('Gift Registry Search');
    }

    /**
     * Retrieve by key saved in session form data
     *
     * @param string $key
     * @return mixed
     */
    public function getFormData($key)
    {
        if (is_null($this->_formData)) {
            $this->_formData = Mage::getSingleton('Magento_Customer_Model_Session')->getRegistrySearchData();
        }
        if (!$this->_formData || !isset($this->_formData[$key])) {
            return null;
        }
        return $this->escapeHtml($this->_formData[$key]);
    }

    /**
     * Return available gift registry types collection
     *
     * @return Enterprise_GiftRegistry_Model_Resource_Type_Collection
     */
    public function getTypesCollection()
    {
        return Mage::getModel('Enterprise_GiftRegistry_Model_Type')->getCollection()
            ->addStoreData(Mage::app()->getStore()->getId());
    }

    /**
     * Select element for choosing registry type
     *
     * @return array
     */
    public function getTypeSelectHtml()
    {
        $select = $this->getLayout()->createBlock('Magento_Core_Block_Html_Select')
            ->setData(array(
                'id'    => 'params-type-id',
                'class' => 'select'
            ))
            ->setName('params[type_id]')
            ->setOptions($this->getTypesCollection()->toOptionArray(true));
        return $select->getHtml();
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        return $this->getUrl('giftregistry/search/results');
    }

    /**
     * Return search form action url
     *
     * @return string
     */
    public function getAdvancedUrl()
    {
        return $this->getUrl('giftregistry/search/advanced');
    }
}
