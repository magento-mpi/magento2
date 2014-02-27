<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Paypal\Model\System\Config\Source;

/**
 * Source model for url method: GET/POST
 */
class UrlMethod implements \Magento\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'GET', 'label' => 'GET'),
            array('value' => 'POST', 'label' => 'POST'),
        );
    }
}
