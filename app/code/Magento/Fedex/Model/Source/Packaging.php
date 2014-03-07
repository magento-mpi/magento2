<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Fedex packaging source implementation
 *
 * @category   Magento
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Fedex\Model\Source;

class Packaging extends \Magento\Fedex\Model\Source\Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'packaging';
}
