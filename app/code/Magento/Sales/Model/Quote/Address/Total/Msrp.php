<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Quote\Address\Total;

/**
 * Msrp items total
 * Collects flag if MSRP price is in use
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
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
    public function __construct(\Magento\Catalog\Helper\Data $catalogData)
    {
        $this->_catalogData = $catalogData;
    }

    /**
     * Collect information about MSRP price enabled
     *
     * @param  \Magento\Sales\Model\Quote\Address $address
     * @return $this
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
            if (!$item->getParentItemId() && $this->_catalogData->isShowBeforeOrderConfirm($item->getProductId())) {
                $canApplyMsrp = true;
                break;
            }
        }

        $address->setCanApplyMsrp($canApplyMsrp);

        return $this;
    }
}
