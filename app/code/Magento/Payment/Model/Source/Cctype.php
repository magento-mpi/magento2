<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment CC Types Source Model
 *
 * @category    Magento
 * @package     Magento_Payment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Payment_Model_Source_Cctype
{
    /**
     * Allowed CC types
     *
     * @var array
     */
    protected $_allowedTypes = array();

    /**
     * Return allowed cc types for current method
     *
     * @return array
     */
    public function getAllowedTypes()
    {
        return $this->_allowedTypes;
    }

    /**
     * Setter for allowed types
     *
     * @param $values
     * @return Magento_Payment_Model_Source_Cctype
     */
    public function setAllowedTypes(array $values)
    {
        $this->_allowedTypes = $values;
        return $this;
    }

    public function toOptionArray()
    {
        /**
         * making filter by allowed cards
         */
        $allowed = $this->getAllowedTypes();
        $options = array();

        foreach (Mage::getSingleton('Magento_Payment_Model_Config')->getCcTypes() as $code => $name) {
            if (in_array($code, $allowed) || !count($allowed)) {
                $options[] = array(
                   'value' => $code,
                   'label' => $name
                );
            }
        }

        return $options;
    }
}
