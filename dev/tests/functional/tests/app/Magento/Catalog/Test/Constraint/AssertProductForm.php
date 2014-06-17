<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AssertForm;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductEdit;

/**
 * Class AssertProductForm
 */
class AssertProductForm extends AssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert form data equals fixture data
     *
     * @param FixtureInterface $product
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductEdit $productPage
     * @return void
     */
    public function processAssert(
        FixtureInterface $product,
        CatalogProductIndex $productGrid,
        CatalogProductEdit $productPage
    ) {
        $filter = ['sku' => $product->getSku()];
        $productGrid->open()->getProductGrid()->searchAndOpen($filter);

        $fixtureData = $this->prepareFixtureData($product->getData());
        $formData = $this->prepareFormData($productPage->getForm()->getData($product));
        $error = $this->verifyData($fixtureData, $formData);
        \PHPUnit_Framework_Assert::assertTrue(null === $error, $error);
    }

    /**
     * Prepares fixture data for comparison
     *
     * @param array $data
     * @return array
     */
    protected function prepareFixtureData(array $data)
    {
        if (isset($data['website_ids']) && !is_array($data['website_ids'])) {
            $data['website_ids'] = [$data['website_ids']];
        }
        if (isset($data['giftcard_amounts'])) {
            usort($data['giftcard_amounts'], [&$this, 'compareAmounts']);
        }

        return $data;
    }

    /**
     * Prepares form data for comparison
     *
     * @param array $data
     * @return array
     */
    protected function prepareFormData(array $data)
    {
        if (isset($data['giftcard_amounts'])) {
            usort($data['giftcard_amounts'], [&$this, 'compareAmounts']);
        }

        return $data;
    }

    /**
     * User function for compare amounts
     *
     * @param $first
     * @param $second
     * @return int
     */
    public function compareAmounts($first, $second) {
        if ($first['price'] == $second['price']) {
            return 0;
        }
        return ($first['price'] < $second['price']) ? -1 : 1;
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Form data equal the fixture data.';
    }
}
