<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Data source to fill "Forms" field
 *
 * @category   Mage
 * @package    Mage_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Captcha_Model_Config_Form_Abstract extends Mage_Core_Model_Config_Value
{
    /**
     * @var string
     */
    protected $_configPath;

    /**
     * Returns options for form multiselect
     *
     * @return array
     */
    public function toOptionArray()
    {
        $optionArray = array();
        $backendConfig = Mage::getConfig()->getValue($this->_configPath);
        if ($backendConfig) {
            foreach ($backendConfig as $formName => $formConfig) {
                /* @var $formConfig Mage_Core_Model_Config_Element */
                if (!empty($formConfig['label'])) {
                    $optionArray[] = array('label' => $formConfig['label'], 'value' => $formName);
                }
            }
        }
        return $optionArray;
    }
}
