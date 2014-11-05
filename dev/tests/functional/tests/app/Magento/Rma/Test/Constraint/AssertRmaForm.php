<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
 
namespace Magento\Rma\Test\Constraint;

use Magento\Rma\Test\Fixture\Rma;
use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractAssertForm;
use Magento\Rma\Test\Page\Adminhtml\RmaView;
use Magento\Rma\Test\Page\Adminhtml\RmaIndex;
use Magento\Sales\Test\Fixture\OrderInjectable;
use Magento\Customer\Test\Fixture\CustomerInjectable;

/**
 * Assert that displayed rma data on edit page equals passed from fixture.
 */
class AssertRmaForm extends AbstractAssertForm
{
    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Array skipped fields.
     *
     * @var array
     */
    protected $skippedFields = [
        'status',
        'comment',
        'items'
    ];

    /**
     * Assert that displayed rma data on edit page equals passed from fixture.
     */
    public function processAssert(Rma $rma, RmaIndex $rmaIndex, RmaView $rmaView)
    {
        $rmaId = $rma->getEntityId();
        $filter = [
            'rma_id_from' => $rmaId,
            'rma_id_to' => $rmaId
        ];

        $rmaIndex->open();
        $rmaIndex->getRmaGrid()->searchAndOpen($filter);

        $fixtureData = $this->getRmaData($rma);
        $pageData = $rmaView->getRmaForm()->getData($rma);
        $this->verifyDetails($fixtureData, $pageData);
        $this->verifyComment($fixtureData, $pageData);
        $this->verifyItems($fixtureData, $pageData);
    }

    /**
     * Assert that displayed rma details on edit page equals passed from fixture.
     *
     * @param array $fixtureData
     * @param array $pageData
     * @return void
     */
    protected function verifyDetails(array $fixtureData, array $pageData)
    {
        $fixtureDetails = array_diff_key($fixtureData, array_flip($this->skippedFields));
        $pageDetails = array_diff_key($pageData, array_flip($this->skippedFields));

        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureDetails,
            $pageDetails,
            'Displayed rma details on edit page does not equals passed from fixture'
        );
    }

    /**
     * Assert that displayed rma comment on edit page equals passed from fixture.
     *
     * @param array $fixtureData
     * @param array $pageData
     * @return void
     */
    protected function verifyComment(array $fixtureData, array $pageData)
    {
        $fixtureComment = $fixtureData['comment'];
        $pageComments = $pageData['comment'];
        $isVisibleComment = false;

        foreach ($pageComments as $pageComment) {
            if ($pageComment['comment'] == $fixtureComment['comment']) {
                $isVisibleComment = true;
            }
        }

        \PHPUnit_Framework_Assert::assertTrue(
            $isVisibleComment,
            'Displayed rma comment on edit page does not equals passed from fixture.'
        );
    }

    /**
     * Assert that displayed rma items on edit page equals passed from fixture.
     *
     * @param array $fixtureData
     * @param array $pageData
     * @return void
     */
    protected function verifyItems(array $fixtureData, array $pageData)
    {
        $fixtureItems = $this->sortDataByPath($fixtureData['items'], '::sku');
        $pageItems = $this->sortDataByPath($pageData['items'], '::sku');

        foreach ($pageItems as $key => $pageItem) {
            $pageItem['product'] = preg_replace('/ \(.+\)$/', '', $pageItem['product']);
            $pageItems[$key] = array_intersect_key($pageItem, $fixtureItems[$key]);
        }

        \PHPUnit_Framework_Assert::assertEquals(
            $fixtureItems,
            $pageItems,
            'Displayed rma items on edit page does not equals passed from fixture.'
        );
    }

    /**
     * Return rma data.
     *
     * @param Rma $rma
     * @return array
     */
    protected function getRmaData(Rma $rma)
    {
        /** @var OrderInjectable $order */
        $order = $rma->getDataFieldConfig('order_id')['source']->getOrder();
        $orderItems = $order->getEntityId();
        /** @var CustomerInjectable $customer */
        $customer = $order->getDataFieldConfig('customer_id')['source']->getCustomer();

        $data = $rma->getData();
        $data['customer_name'] = sprintf('%s %s', $customer->getFirstname(), $customer->getLastname());
        $data['customer_email'] = $customer->getEmail();

        foreach ($data['items'] as $key => $item) {
            $product = $orderItems[$key];

            $item['product'] = $product->getName();
            $item['sku'] = $this->getItemSku($product);

            $data['items'][$key] = $item;
        }

        return $data;
    }

    /**
     * Return sku of product.
     *
     * @param FixtureInterface $product
     * @return string
     */
    protected function getItemSku(FixtureInterface $product)
    {
        return $product->getSku();
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Correct return request is present on backend.';
    }
}
