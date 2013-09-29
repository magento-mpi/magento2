<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Quote address attribute frontend resource model
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Resource\Quote\Address\Attribute;

class Frontend
    extends \Magento\Eav\Model\Entity\Attribute\Frontend\AbstractFrontend
{
    /**
     * Fetch totals
     *
     * @param \Magento\Sales\Model\Quote\Address $address
     * @return array
     */
    public function fetchTotals(\Magento\Sales\Model\Quote\Address $address)
    {
        $arr = array();
        
        return $arr;
    }
}
