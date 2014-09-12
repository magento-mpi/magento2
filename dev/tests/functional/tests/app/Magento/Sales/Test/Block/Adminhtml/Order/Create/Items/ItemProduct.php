<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Items;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class ItemProduct
 * Item product block
 */
class ItemProduct extends Block
{
    /**
     * Fields
     *
     * @var array
     */
    protected $fields = [
        'name' => [
            'selector' => '.col-product span',
            'strategy' => Locator::SELECTOR_CSS,
            'input' => null,
            'action' => 'getText'
        ],
        'price' => [
            'selector' => '.col-price span.price',
            'strategy' => Locator::SELECTOR_CSS,
            'input' => null,
            'action' => 'getText'
        ],
        'qty' => [
            'selector' => '.col-qty input',
            'strategy' => Locator::SELECTOR_CSS,
            'input' => null,
            'action' => 'getValue'
        ],
    ];

    /**
     * Get data item products
     *
     * @param array $fields
     * @param string $currency [optional]
     * @return array
     */
    public function getData(array $fields, $currency = '$')
    {
        $result = [];
        foreach ($fields as $item) {
            $value = $this->_rootElement->find(
                $this->fields[$item]['selector'],
                $this->fields[$item]['strategy'],
                $this->fields[$item]['input']
            )->{$this->fields[$item]['action']}();

            $result[$item] = str_replace($currency, '', trim($value));
        }

        return $result;
    }
}
