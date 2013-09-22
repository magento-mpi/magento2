<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Store Contact Information source model
 *
 * @category   Magento
 * @package    Magento_Core
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Model\Source\Email;

class Variables implements \Magento\Core\Model\Options\ArrayInterface
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
                'value' => \Magento\Core\Model\Url::XML_PATH_UNSECURE_URL,
                'label' => __('Base Unsecure URL')
            ),
            array(
                'value' => \Magento\Core\Model\Url::XML_PATH_SECURE_URL,
                'label' => __('Base Secure URL')
            ),
            array(
                'value' => 'trans_email/ident_general/name',
                'label' => __('General Contact Name')
            ),
            array(
                'value' => 'trans_email/ident_general/email',
                'label' => __('General Contact Email')
            ),
            array(
                'value' => 'trans_email/ident_sales/name',
                'label' => __('Sales Representative Contact Name')
            ),
            array(
                'value' => 'trans_email/ident_sales/email',
                'label' => __('Sales Representative Contact Email')
            ),
            array(
                'value' => 'trans_email/ident_custom1/name',
                'label' => __('Custom1 Contact Name')
            ),
            array(
                'value' => 'trans_email/ident_custom1/email',
                'label' => __('Custom1 Contact Email')
            ),
            array(
                'value' => 'trans_email/ident_custom2/name',
                'label' => __('Custom2 Contact Name')
            ),
            array(
                'value' => 'trans_email/ident_custom2/email',
                'label' => __('Custom2 Contact Email')
            ),
            array(
                'value' => 'general/store_information/name',
                'label' => __('Store Name')
            ),
            array(
                'value' => 'general/store_information/phone',
                'label' => __('Store Phone Number')
            ),
            array(
                'value' => 'general/store_information/country_id',
                'label' => __('Country')
            ),
            array(
                'value' => 'general/store_information/region_id',
                'label' => __('Region/State')
            ),
            array(
                'value' => 'general/store_information/postcode',
                'label' => __('Zip/Postal Code')
            ),
            array(
                'value' => 'general/store_information/city',
                'label' => __('City')
            ),
            array(
                'value' => 'general/store_information/street_line1',
                'label' => __('Street Address 1')
            ),
            array(
                'value' => 'general/store_information/street_line2',
                'label' => __('Street Address 2')
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
                'label' => __('Store Contact Information'),
                'value' => $optionArray
            );
        }
        return $optionArray;
    }
}
