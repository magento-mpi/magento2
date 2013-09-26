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
 * Msrp items total
 * Collects flag if MSRP price is in use
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model\Quote\Address\Total;

class Msrp extends \Magento\Sales\Model\Quote\Address\Total\AbstractTotal
{
    /**
     * Catalog data
     *
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * @param \Magento\Catalog\Helper\Data $catalogData
     */
    public function __construct(
        \Magento\Catalog\Helper\Data $catalogData
    ) {
        $this->_catalogData = $catalogData;
    }

    /**
     * Collect information about MSRP price enabled
     *
     * @param   \Magento\Sales\Model\Quote\Address $address
     * @return  \Magento\Sales\Model\Quote\Address\Total\Msrp
     */
    public function collect(\Magento\Sales\Model\Quote\Address $address)
    {
        parent::collect($address);

        $items = $this->_getAddressItems($address);
        if (!count($items)) {
            return $this;
        }

        $canApplyMsrp = false;
        foreach ($items as $item) {
            if (!$item->getParentItemId() && $this->_catalogData->canApplyMsrp(
                $item->getProductId(),
                \Magento\Catalog\Model\Product\Attribute\Source\Msrp\Type::TYPE_BEFORE_ORDER_CONFIRM,
                true
            )) {
                $canApplyMsrp = true;
                break;
            }
        }

        $address->setCanApplyMsrp($canApplyMsrp);

        return $this;
    }
}
