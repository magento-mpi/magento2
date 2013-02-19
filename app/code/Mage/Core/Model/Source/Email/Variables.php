<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Store Contact Information source model
 *
 * @category   Mage
 * @package    Mage_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Source_Email_Variables
{
    /**
     * Assoc array of configuration variables
     *
     * @var array
     */
    protected $_configVariables = array();

    /**
     * Constructor
     *
     */
    public function __construct()
    {
        $this->_configVariables = array(
            array(
                'value' => Mage_Core_Model_Url::XML_PATH_UNSECURE_URL,
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Base Unsecure URL')
            ),
            array(
                'value' => Mage_Core_Model_Url::XML_PATH_SECURE_URL,
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Base Secure URL')
            ),
            array(
                'value' => 'trans_email/ident_general/name',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('General Contact Name')
            ),
            array(
                'value' => 'trans_email/ident_general/email',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('General Contact Email')
            ),
            array(
                'value' => 'trans_email/ident_sales/name',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Sales Representative Contact Name')
            ),
            array(
                'value' => 'trans_email/ident_sales/email',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Sales Representative Contact Email')
            ),
            array(
                'value' => 'trans_email/ident_custom1/name',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Custom1 Contact Name')
            ),
            array(
                'value' => 'trans_email/ident_custom1/email',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Custom1 Contact Email')
            ),
            array(
                'value' => 'trans_email/ident_custom2/name',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Custom2 Contact Name')
            ),
            array(
                'value' => 'trans_email/ident_custom2/email',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Custom2 Contact Email')
            ),
            array(
                'value' => 'general/store_information/name',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Store Name')
            ),
            array(
                'value' => 'general/store_information/phone',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Store Contact Telephone')
            ),
            array(
                'value' => 'general/store_information/address',
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Store Contact Address')
            )
        );
    }

    /**
     * Retrieve option array of store contact variables
     *
     * @param boolean $withGroup
     * @return array
     */
    public function toOptionArray($withGroup = false)
    {
        $optionArray = array();
        foreach ($this->_configVariables as $variable) {
            $optionArray[] = array(
                'value' => '{{config path="' . $variable['value'] . '"}}',
                'label' => $variable['label']
            );
        }
        if ($withGroup && $optionArray) {
            $optionArray = array(
                'label' => Mage::helper('Mage_Core_Helper_Data')->__('Store Contact Information'),
                'value' => $optionArray
            );
        }
        return $optionArray;
    }
}
