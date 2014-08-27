<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Msrp\Block\Adminhtml\Product\Helper\Form;

/**
 * Product form MSRP field helper
 */
class Type extends \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Price
{
    /** @var \Magento\Msrp\Helper\Data */
    protected $msrpData;

    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Msrp\Helper\Data $msrpData,
        array $data = array()
    ) {
        parent::__construct(
            $factoryElement,
            $factoryCollection,
            $escaper,
            $storeManager,
            $localeCurrency,
            $taxData,
            $data
        );
        $this->msrpData = $msrpData;
    }

    /**
     * Get the html.
     *
     * @return mixed
     */
    public function toHtml()
    {
        if ($this->msrpData->isMsrpEnabled()) {
            return parent::toHtml();
        }
        return '';
    }
}
