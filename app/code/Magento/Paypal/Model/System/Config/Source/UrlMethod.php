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
 * Source model for url method: GET/POST
 */
namespace Magento\Paypal\Model\System\Config\Source;

class UrlMethod
{
    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 'GET', 'label' => 'GET'),
            array('value' => 'POST', 'label' => 'POST'),
        );
    }
}
