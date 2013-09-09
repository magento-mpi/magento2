<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Renderer for service JavaScript code that disables corresponding paypal methods on page load
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Paypal_Block_Adminhtml_System_Config_Fieldset_Store
    extends Magento_Backend_Block_Template
    implements Magento_Data_Form_Element_Renderer_Interface
{
    /**
     * Path to template file
     *
     * @var string
     */
    protected $_template = 'Magento_Paypal::system/config/fieldset/store.phtml';

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     * @param array $data
     */
    public function __construct(
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_Config $coreConfig,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $data
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Render service JavaScript code
     *
     * @param Magento_Data_Form_Element_Abstract $element
     * @return string
     */
    public function render(Magento_Data_Form_Element_Abstract $element)
    {
        $html = Mage::getModel('Magento_Core_Model_StoreManagerInterface')->isSingleStoreMode() ? '' : $this->toHtml();
        return $html;
    }

    /**
     * Returns list of disabled (in the Default or the Website Scope) paypal methods
     *
     * @return array
     */
    protected function getPaypalDisabledMethods()
    {
        // Assoc array that contains info about paypal methods (their IDs and corresponding Config Paths)
        $methods = array(
            'express'   => 'payment/paypal_express/active',
            'wps'       => 'payment/paypal_standard/active',
            'wpp'       => 'payment/paypal_direct/active',
            'wpppe'     => 'payment/paypaluk_direct/active',
            'verisign'  => 'payment/verisign/active',
            'expresspe' => 'payment/paypaluk_express/active'
        );
        // Retrieve a code of the current website
        $website = $this->getRequest()->getParam('website');
        $disabledMethods = array();
        foreach ($methods as $methodId => $methodPath) {
            $isEnabled = (int)  Mage::getConfig()->getValue($methodPath, 'website', $website);
            if ($isEnabled === 0) {
                $disabledMethods[$methodId] = $isEnabled;
            }
        }
        return $disabledMethods;
    }
}
