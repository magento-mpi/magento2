<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Block\Returns\View;

use Magento\Backend\Test\Block\Widget\Form;
use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * Rma items.
 */
class RmaItems extends Form
{
    /**
     * Locator for item row.
     *
     * @var string
     */
    protected $itemRow = 'tbody tr';

    /**
     * Get data of rma items.
     * @param FixtureInterface|null $fixture
     * @param Element|null $element
     * @return array
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData(FixtureInterface $fixture = null, Element $element = null)
    {
        $context = $element ? $element : $this->_rootElement;
        $mapping = $this->dataMapping();
        $items = $context->find($this->itemRow)->getElements();
        $data = [];

        foreach ($items as $key => $item) {
            foreach ($mapping as $name => $locator) {
                $value = $item->find($locator['selector'], $locator['strategy'])->getText();
                $data[$key][$name] = trim($value);
            }
        }

        return $data;
    }
}
