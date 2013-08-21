<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Source for email send method
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Config_Source_Email_Method implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        $options    = array(
            array(
                'value' => 'bcc',
                'label' => __('Bcc')
            ),
            array(
                'value' => 'copy',
                'label' => __('Separate Email')
            ),
        );
        return $options;
    }
}
