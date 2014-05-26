<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Block\Advanced;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;
use Mtf\Block\Form as ParentForm;

/**
 * Class Form
 * Advanced search form
 */
class Form extends ParentForm
{
    /**
     * Search button selector
     *
     * @var string
     */
    protected $searchButtonSelector = '.action.search';

    /**
     * Field selector select tax class
     *
     * @var string
     */
    protected $taxClassSelector = '#tax_class_id';

    /**
     * Submit search form
     *
     * @return void
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector)->click();
    }

    /**
     * Fill the root form
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        // Prepare price data
        $data = $fixture->getData();
        $data = array_merge($data, $data['price']);
        unset($data['price']);

        foreach ($data as $key => $value) {
            if ($value === '-') {
                unset($data[$key]);
            }
        }

        // If you want to select all, we obtain values
        if ($data['tax_class_id'] === 'all') {
            $taxClassValues = explode(
                "\n",
                $this->_rootElement->find($this->taxClassSelector)->getText()
            );
            $data['tax_class_id'] = $taxClassValues;
        }

        // Mapping
        $mapping = $this->dataMapping($data);
        $this->_fill($mapping, $element);

        return $this;
    }
} 