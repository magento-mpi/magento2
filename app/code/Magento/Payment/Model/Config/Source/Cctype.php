<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Payment
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Payment_Model_Config_Source_Cctype implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Payment config model
     *
     * @var Magento_Payment_Model_Config
     */
    protected $_paymentConfig;

    /**
     * Construct
     *
     * @param Magento_Payment_Model_Config $paymentConfig
     */
    function __construct(Magento_Payment_Model_Config $paymentConfig)
    {
        $this->_paymentConfig = $paymentConfig;
    }

    public function toOptionArray()
    {
        $options =  array();

        foreach ($this->_paymentConfig->getCcTypes() as $code => $name) {
            $options[] = array(
               'value' => $code,
               'label' => $name
            );
        }

        return $options;
    }
}
