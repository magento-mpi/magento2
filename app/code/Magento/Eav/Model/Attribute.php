<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * EAV attribute resource model (Using Forms)
 *
 * @method Magento_Eav_Model_Attribute_Data_Abstract|null getDataModel() Get data model linked to attribute or null.
 * @method string|null getFrontendInput() Get attribute type for user interface form or null
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Eav_Model_Attribute extends Magento_Eav_Model_Entity_Attribute
{
    /**
     * Name of the module
     * Override it
     */
    //const MODULE_NAME = 'Magento_Eav';

    /**
     * Prefix of model events object
     *
     * @var string
     */
    protected $_eventObject = 'attribute';

    /**
     * Active Website instance
     *
     * @var Magento_Core_Model_Website
     */
    protected $_website;

    /**
     * Set active website instance
     *
     * @param Magento_Core_Model_Website|int $website
     * @return Magento_Eav_Model_Attribute
     */
    public function setWebsite($website)
    {
        $this->_website = Mage::app()->getWebsite($website);
        return $this;
    }

    /**
     * Return active website instance
     *
     * @return Magento_Core_Model_Website
     */
    public function getWebsite()
    {
        if (is_null($this->_website)) {
            $this->_website = Mage::app()->getWebsite();
        }

        return $this->_website;
    }

    /**
     * Processing object after save data
     *
     * @return Magento_Eav_Model_Attribute
     */
    protected function _afterSave()
    {
        Mage::getSingleton('Magento_Eav_Model_Config')->clear();
        return parent::_afterSave();
    }

    /**
     * Return forms in which the attribute
     *
     * @return array
     */
    public function getUsedInForms()
    {
        $forms = $this->getData('used_in_forms');
        if (is_null($forms)) {
            $forms = $this->_getResource()->getUsedInForms($this);
            $this->setData('used_in_forms', $forms);
        }
        return $forms;
    }

    /**
     * Return validate rules
     *
     * @return array
     */
    public function getValidateRules()
    {
        $rules = $this->getData('validate_rules');
        if (is_array($rules)) {
            return $rules;
        } else if (!empty($rules)) {
            return unserialize($rules);
        }
        return array();
    }

    /**
     * Set validate rules
     *
     * @param array|string $rules
     * @return Magento_Eav_Model_Attribute
     */
    public function setValidateRules($rules)
    {
        if (empty($rules)) {
            $rules = null;
        } else if (is_array($rules)) {
            $rules = serialize($rules);
        }
        $this->setData('validate_rules', $rules);

        return $this;
    }

    /**
     * Return scope value by key
     *
     * @param string $key
     * @return mixed
     */
    protected function _getScopeValue($key)
    {
        $scopeKey = sprintf('scope_%s', $key);
        if ($this->getData($scopeKey) !== null) {
            return $this->getData($scopeKey);
        }
        return $this->getData($key);
    }

    /**
     * Return is attribute value required
     *
     * @return mixed
     */
    public function getIsRequired()
    {
        return $this->_getScopeValue('is_required');
    }

    /**
     * Return is visible attribute flag
     *
     * @return mixed
     */
    public function getIsVisible()
    {
        return $this->_getScopeValue('is_visible');
    }

    /**
     * Return default value for attribute
     *
     * @return mixed
     */
    public function getDefaultValue()
    {
        return $this->_getScopeValue('default_value');
    }

    /**
     * Return count of lines for multiply line attribute
     *
     * @return mixed
     */
    public function getMultilineCount()
    {
        return $this->_getScopeValue('multiline_count');
    }
}
