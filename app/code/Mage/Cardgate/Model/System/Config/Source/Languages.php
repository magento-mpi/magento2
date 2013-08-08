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
                "label" => __('Dutch')
            ),
            array(
                "value" => "en",
                "label" => __('English')
            ),
            array(
                "value" => "de",
                "label" => __('German')
            ),
            array(
                "value" => "fr",
                "label" => __('French')
            ),
            array(
                "value" => "es",
                "label" => __('Spanish')
            ),
            array(
                "value" => "gr",
                "label" => __('Greek')
            ),
            array(
                "value" => "hr",
                "label" => __('Croatian')
            ),
            array(
                "value" => "it",
                "label" => __('Italian')
            ),
            array(
                "value" => "cz",
                "label" => __('Czech')
            ),
            array(
                "value" => "ru",
                "label" => __('Russian')
            ),
            array(
                "value" => "se",
                "label" => __('Swedish')
            ),
        );
    }
}
