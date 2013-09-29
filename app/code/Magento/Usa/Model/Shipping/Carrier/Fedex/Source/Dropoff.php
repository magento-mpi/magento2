<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Usa
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fedex dropoff source implementation
 *
 * @category   Magento
 * @package    Magento_Usa
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Usa\Model\Shipping\Carrier\Fedex\Source;

class Dropoff extends \Magento\Usa\Model\Shipping\Carrier\Fedex\Source\Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'dropoff';
}
