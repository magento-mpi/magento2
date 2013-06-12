<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Cardgate
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @category   Mage
 * @package    Mage_Cardgate
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Cardgate_Block_Form_Creditcard extends Mage_Payment_Block_Form
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
     * @var Mage_Payment_Model_Config
     */
    protected $_paymentConfig;

    /**
     * Constructor
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Payment_Model_Config $paymentConfig
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Payment_Model_Config $paymentConfig,
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
                foreach ($types as $code => $name) {
                    if (!in_array($code, $availableTypes)) {
                        unset($types[$code]);
                    }
                }
            }
        }
        return implode($types, ', ');
    }
}
