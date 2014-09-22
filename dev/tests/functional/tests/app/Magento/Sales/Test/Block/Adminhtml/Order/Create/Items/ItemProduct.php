<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create\Items;

use Mtf\Block\Form;
use Mtf\Client\Element;

/**
 * Class ItemProduct
 * Item product block
 */
class ItemProduct extends Form
{
    /**
     * Fields
     *
     * @var array
     */
    protected $actions = [
        'name' => 'getText',
        'price' => 'getText',
        'checkout_data' => 'getValue',
    ];

    /**
     * Get data item products
     *
     * @param array $fields
     * @param string $currency [optional]
     * @return array
     */
    public function getCheckoutData(array $fields, $currency = '$')
    {
        $result = [];
        $data = $this->dataMapping($fields);
        foreach ($data as $key => $item) {
            if (!isset($item['value'])) {
                $result[$key] = $this->_getData($item);
                continue;
            }
            $value = $this->_rootElement->find(
                $item['selector'],
                $item['strategy'],
                $item['input']
            )->{$this->actions[$key]}();

            $result[$key] = str_replace($currency, '', trim($value));
        }

        return $result;
    }
}
