<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Magento_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Cardgate_Block_Form_Creditcard extends Magento_Payment_Block_Form
{
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'form/creditcard.phtml';

    /**
     * Form Factory
     *
     * @var Magento_Payment_Model_Config
     */
    protected $_paymentConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Payment_Model_Config $paymentConfig
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Payment_Model_Config $paymentConfig,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->_paymentConfig = $paymentConfig;
    }

    /**
     * Returns allowed Card Types
     *
     * @return string
     */
    public function getAllowedCardTypes()
    {
        $types = $this->_paymentConfig->getCcTypes();
        if ($method = $this->getMethod()) {
            $availableTypes = $method->getConfigData('cctypes');
            if ($availableTypes) {
                $availableTypes = explode(',', $availableTypes);
                foreach (array_keys($types) as $code) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return implode($types, ', ');
    }
}
