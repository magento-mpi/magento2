<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Source for email send method
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Model\Config\Source\Email;

class Method implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('value' => 'bcc', 'label' => __('Bcc')),
            array('value' => 'copy', 'label' => __('Separate Email'))
        );
        return $options;
    }
}
