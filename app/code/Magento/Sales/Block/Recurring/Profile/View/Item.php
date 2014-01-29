<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Recurring\Profile\View;

/**
 * Recurring profile view item
 */
class Item extends \Magento\Sales\Block\Recurring\Profile\View
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
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Catalog\Model\Product\Option $option
     * @param \Magento\Catalog\Model\Product $product
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Catalog\Model\Product\Option $option,
        \Magento\Catalog\Model\Product $product,
        array $data = array()
    ) {
        $this->_option = $option;
        $this->_product = $product;
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

        foreach (array(
            'name' => __('Product Name'),
            'sku' => __('SKU'),
            'qty' => __('Quantity'),
        ) as $itemKey => $label) {
            $value = $this->_recurringProfile->getInfoValue($key, $itemKey);
            if ($value) {
                $this->_addInfo(array('label' => $label, 'value' => $value,));
            }
        }

        $request = $this->_recurringProfile->getInfoValue($key, 'info_buyRequest');
        if (empty($request)) {
            return;
        }

        $request = unserialize($request);
        if (empty($request['options'])) {
            return;
        }

        $options = $this->_option->getCollection()
            ->addIdsToFilter(array_keys($request['options']))
            ->addTitleToResult($this->_recurringProfile->getInfoValue($key, 'store_id'))
            ->addValuesToResult();

        foreach ($options as $option) {
            $this->_option->setId($option->getId());

            $group = $option->groupFactory($option->getType())
                ->setOption($option)
                ->setRequest(new \Magento\Object($request))
                ->setProduct($this->_product)
                ->setUseQuotePath(true)
                ->setQuoteItemOption($this->_option)
                ->validateUserValue($request['options']);

            $skipHtmlEscaping = false;
            if ('file' == $option->getType()) {
                $skipHtmlEscaping = true;

                $downloadParams = array(
                    'id'  => $this->_recurringProfile->getId(),
                    'option_id' => $option->getId(),
                    'key' => $request['options'][$option->getId()]['secret_key']
                );
                $group->setCustomOptionDownloadUrl('sales/download/downloadProfileCustomOption')
                    ->setCustomOptionUrlParams($downloadParams);
            }

            $optionValue = $group->prepareForCart();

            $this->_addInfo(array(
                'label' => $option->getTitle(),
                'value' => $group->getFormattedOptionValue($optionValue),
                'skip_html_escaping' => $skipHtmlEscaping
            ));
        }
    }
}
