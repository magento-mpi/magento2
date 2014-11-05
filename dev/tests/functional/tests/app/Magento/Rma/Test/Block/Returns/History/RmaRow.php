<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Test\Block\Returns\History;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * Rma row in table.
 */
class RmaRow extends \Magento\Backend\Test\Block\Widget\Form
{
    /**
     * Locator for action "View Return".
     *
     * @var string
     */
    protected $actionView = '.view';

    /**
     * Get data of rma row.
     *
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
        $data = [];

        foreach ($mapping as $name => $field) {
            $data[$name] = $context->find($field['selector'], $field['strategy'])->getText();
        }

        return $data;
    }

    /**
     * Click in action link "View Return".
     *
     * @return void
     */
    public function clickView()
    {
        $this->_rootElement->find($this->actionView)->click();
    }
}
