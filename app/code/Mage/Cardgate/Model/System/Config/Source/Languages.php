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
class Mage_Cardgate_Model_System_Config_Source_Languages
{
    /**
     * Helper object
     *
     * @var Mage_Cardgate_Helper_Data
     */
    protected $_helper;

    /**
     * Constructor
     *
     * @param Mage_Cardgate_Helper_Data $helper
     */
    public function __construct(Mage_Cardgate_Helper_Data $helper)
    {
        $this->_helper = $helper;
    }

    /**
     * Returns languages available for CardGate
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                "value" => "nl",
                "label" => $this->_helper->__('Dutch')
            ),
            array(
                "value" => "en",
                "label" => $this->_helper->__('English')
            ),
            array(
                "value" => "de",
                "label" => $this->_helper->__('German')
            ),
            array(
                "value" => "fr",
                "label" => $this->_helper->__('French')
            ),
            array(
                "value" => "es",
                "label" => $this->_helper->__('Spanish')
            ),
            array(
                "value" => "gr",
                "label" => $this->_helper->__('Greek')
            ),
            array(
                "value" => "hr",
                "label" => $this->_helper->__('Croatian')
            ),
            array(
                "value" => "it",
                "label" => $this->_helper->__('Italian')
            ),
            array(
                "value" => "cz",
                "label" => $this->_helper->__('Czech')
            ),
            array(
                "value" => "ru",
                "label" => $this->_helper->__('Russian')
            ),
            array(
                "value" => "se",
                "label" => $this->_helper->__('Swedish')
            ),
        );
    }
}
