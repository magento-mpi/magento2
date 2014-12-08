<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment\View;

/**
 * Recurring payment view item
 *
 * @SuppressWarnings(PHPMD.DepthOfInheritance)
 */
class Item extends \Magento\RecurringPayment\Block\Payment\View
{
    /**
     * @var \Magento\Catalog\Model\Product\Option
     */
    protected $_option;

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $_product;

    /**
     * @var \Magento\Sales\Model\Quote\Item\OptionFactory
     */
    protected $_quoteItemOptionFactory;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Sales\Model\Quote\Item\OptionFactory $quoteItemOptionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Model\Product\Option $option,
        \Magento\Catalog\Model\Product $product,
        \Magento\Sales\Model\Quote\Item\OptionFactory $quoteItemOptionFactory,
        array $data = []
    ) {
        $this->_option = $option;
        $this->_product = $product;
        $this->_quoteItemOptionFactory = $quoteItemOptionFactory;
        parent::__construct($context, $registry, $data);
    }

    /**
     * Prepare item info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_shouldRenderInfo = true;
        $key = 'order_item_info';

        foreach ([
            'name' => __('Product Name'),
            'sku' => __('SKU'),
            'qty' => __('Quantity'),
        ] as $itemKey => $label) {
            $value = $this->_recurringPayment->getInfoValue($key, $itemKey);
            if ($value) {
                $this->_addInfo(['label' => $label, 'value' => $value]);
            }
        }

        $request = $this->_recurringPayment->getInfoValue($key, 'info_buyRequest');
        if (empty($request)) {
            return;
        }

        $request = unserialize($request);
        if (empty($request['options'])) {
            return;
        }

        $options = $this->_option->getCollection()->addIdsToFilter(
            array_keys($request['options'])
        )->addTitleToResult(
            $this->_recurringPayment->getInfoValue($key, 'store_id')
        )->addValuesToResult();

        foreach ($options as $option) {
            $quoteItemOption = $this->_quoteItemOptionFactory->create()->setId($option->getId());

            $group = $option->groupFactory(
                $option->getType()
            )->setOption(
                $option
            )->setRequest(
                new \Magento\Framework\Object($request)
            )->setProduct(
                $this->_product
            )->setUseQuotePath(
                true
            )->setQuoteItemOption(
                $quoteItemOption
            )->validateUserValue(
                $request['options']
            );

            $skipHtmlEscaping = false;
            if ('file' == $option->getType()) {
                $skipHtmlEscaping = true;

                $downloadParams = [
                    'id' => $this->_recurringPayment->getId(),
                    'option_id' => $option->getId(),
                    'key' => $request['options'][$option->getId()]['secret_key'],
                ];
                $group->setCustomOptionDownloadUrl(
                    'sales/download/downloadProfileCustomOption'
                )->setCustomOptionUrlParams(
                    $downloadParams
                );
            }

            $optionValue = $group->prepareForCart();

            $this->_addInfo(
                [
                    'label' => $option->getTitle(),
                    'value' => $group->getFormattedOptionValue($optionValue),
                    'skip_html_escaping' => $skipHtmlEscaping,
                ]
            );
        }
    }
}
