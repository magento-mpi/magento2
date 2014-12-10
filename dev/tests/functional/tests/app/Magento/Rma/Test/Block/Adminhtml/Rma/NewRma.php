<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Rma\Test\Fixture\Rma\OrderId;
use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;

/**
 * Rma new page tabs.
 */
class NewRma extends FormTabs
{
    /**
     * Fill form with tabs.
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return FormTabs
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $tabs = $this->getFieldsByTabs($fixture);
        if (isset($tabs['items']['items']['value'])) {
            $orderItems = $this->getOrderItems($fixture);
            $tabs['items']['items']['value'] = $this->prepareItems($orderItems, $tabs['items']['items']['value']);
        }

        return $this->fillTabs($tabs, $element);
    }

    /**
     * Get order items from rma fixture.
     *
     * @param InjectableFixture $fixture
     * @return array
     */
    protected function getOrderItems(InjectableFixture $fixture)
    {
        /** @var OrderId $sourceOrderId */
        $sourceOrderId = $fixture->getDataFieldConfig('order_id')['source'];
        return $sourceOrderId->getOrder()->getEntityId()['products'];
    }

    /**
     * Prepare items data.
     *
     * @param array $orderItems
     * @param array $items
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedLocalVariable)
     */
    protected function prepareItems(array $orderItems, array $items)
    {
        foreach ($items as $productKey => $productData) {
            $key = str_replace('product_key_', '', $productKey);
            $items[$productKey]['product'] = $orderItems[$key];
        }
        return $items;
    }
}
