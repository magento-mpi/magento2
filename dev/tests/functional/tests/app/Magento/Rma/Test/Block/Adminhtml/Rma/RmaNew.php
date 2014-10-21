<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Adminhtml\Rma;

use Mtf\Client\Element;
use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\InjectableFixture;
use Magento\Rma\Test\Fixture\Rma;
use Magento\Rma\Test\Fixture\Rma\OrderId;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class RmaNew
 * Rma new page tabs.
 */
class RmaNew extends FormTabs
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
