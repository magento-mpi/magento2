<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

/**
 * Product form MSRP field helper
 */
class Msrp extends Price
{
    /** @var \Magento\Catalog\Helper\Data */
    protected $catalogData;

    public function __construct(
        \Magento\Framework\Data\Form\Element\Factory $factoryElement,
        \Magento\Framework\Data\Form\Element\CollectionFactory $factoryCollection,
        \Magento\Framework\Escaper $escaper,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Locale\CurrencyInterface $localeCurrency,
        \Magento\Tax\Helper\Data $taxData,
        \Magento\Catalog\Helper\Data $catalogData,
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
        $this->catalogData = $catalogData;
    }

    /**
     * Get the html.
     *
     * @return mixed
     */
    public function toHtml()
    {
        if ($this->catalogData->isMsrpEnabled()) {
            return parent::toHtml();
        }
        return '';
    }
}
