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
namespace Magento\Backend\Model\Config\Source\Email;

class Method implements \Magento\Core\Model\Option\ArrayInterface
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
