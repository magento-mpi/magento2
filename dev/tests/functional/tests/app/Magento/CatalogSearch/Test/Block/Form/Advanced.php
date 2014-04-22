<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Test\Block\Form;

use Mtf\Fixture\FixtureInterface;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Advanced form search block
 *
 * @package Magento\CatalogSearch\Test\Block\Form
 */
class Advanced extends Form
{
    /**
     * Search button selector
     *
     * @var string
     */
    protected $searchButtonSelector = '.action.search';

    /**
     * Fill form with custom fields
     *
     * @param FixtureInterface $fixture
     * @param array $fields
     * @param Element $element
     */
    public function fillCustom(FixtureInterface $fixture, array $fields, Element $element = null)
    {
        $data = $fixture->getData('fields');
        $dataForMapping = array_intersect_key($data, array_flip($fields));
        $mapping = $this->dataMapping($dataForMapping);
        $this->_fill($mapping, $element);
    }

    /**
     * Submit search form
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector, Locator::SELECTOR_CSS)->click();
    }
}
