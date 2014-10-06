<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Mtf\Block\Block;

/**
 * Class Create
 * Abstract create block
 */
abstract class AbstractCreate extends Block
{
    /**
     * Form block css selector
     *
     * @var string
     */
    protected $form = '#edit_form';

    /**
     * Fill credit memo data
     *
     * @param array $data
     * @param array|null $products [optional]
     * @return void
     */
    public function fill(array $data, $products = null)
    {
        if (isset($data['form_data'])) {
            $this->getFormBlock()->fillData($data['form_data']);
        }
        if (isset($data['items_data']) && $products !== null) {
            foreach ($products as $key => $product) {
                $this->getItemsBlock()->getItemProductBlock($product)->fillProduct($data['items_data'][$key]);
            }
        }
    }

    /**
     * Click update qty's button
     *
     * @return void
     */
    public function updateQty()
    {
        $this->getItemsBlock()->clickUpdateQty();
    }

    /**
     * Get items block
     *
     * @return AbstractItemsNewBlock
     */
    abstract protected function getItemsBlock();

    /**
     * Get form block
     *
     * @return AbstractForm
     */
    abstract public function getFormBlock();
}
