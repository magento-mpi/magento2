<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Data source to fill "Forms" field
 *
 * @category   Magento
 * @package    Magento_Captcha
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Captcha_Model_Config_Form_Abstract extends Magento_Core_Model_Config_Data
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
        /* @var $backendNode Magento_Core_Model_Config_Element */
        $backendNode = $this->_coreConfig->getNode($this->_configPath);
        if ($backendNode) {
            foreach ($backendNode->children() as $formNode) {
                /* @var $formNode Magento_Core_Model_Config_Element */
                if (!empty($formNode->label)) {
                    $optionArray[] = array('label' => (string)$formNode->label, 'value' => $formNode->getName());
                }
            }
        }
        return $optionArray;
    }
}
