<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Class Options
 * Manage Options form on New Customer Attribute Page
 */
class Options extends Tab
{
    /**
     * 'Add Option' button
     *
     * @var string
     */
    protected $addOption = '#add_new_option_button';

    /**
     * Target row for dragAndDrop options to
     *
     * @var string
     */
    protected $targetElement = '//*[@class="ui-sortable"]/tr[%d]';

    /**
     * Options value locator
     *
     * @var string
     */
    protected $optionSelector = '.input-text.required-option';

    /**
     * Locator of draggable column
     *
     * @var string
     */
    protected $draggableColumn = './../../td[@class="col-draggable"]';

    /**
     * Selector for option row
     *
     * @var string
     */
    protected $optionRowSelector = '//tbody[@data-role="options-container"]/tr[%d]';

    /**
     * Fill 'Options' tab & drag and drop options
     *
     * @param array $fields
     * @param Element|null $element
     * @throws \Exception
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $fixtureOptions = isset($fields['option']['value']) ? $fields['option']['value'] : [];
        foreach ($fixtureOptions as $key => $option) {
            $row = $this->_rootElement->find(sprintf($this->optionRowSelector, $key + 1), Locator::SELECTOR_XPATH);
            if (!$row->isVisible()) {
                $this->_rootElement->find($this->addOption)->click();
            }
            $this->blockFactory->create(
                'Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\Edit\Tab\Options\Option',
                ['element' => $row]
            )->fillOptions($option);
        }

        $optionElements = $this->_rootElement->find($this->optionSelector, Locator::SELECTOR_CSS)->getElements();

        $this->sortOptions($fixtureOptions, $optionElements);

        return $this;
    }

    /**
     * Sort options according to fixture order
     *
     * @param array $fixtureOptions
     * @param array $optionElements
     * @throws \Exception
     * @return void
     */
    protected function sortOptions(array $fixtureOptions, array $optionElements)
    {
        foreach ($fixtureOptions as $fixtureOption) {
            if ($fixtureOption['order'] > count($optionElements)) {
                throw new \Exception("Order number of options from fixture is greater than form options number.");
            }
            $target = $this->_rootElement->find(
                sprintf($this->targetElement, $fixtureOption['order']),
                Locator::SELECTOR_XPATH
            );
            foreach ($optionElements as $optionElement) {
                if ($optionElement->getValue() === $fixtureOption['admin']) {
                    $optionElement->find($this->draggableColumn, Locator::SELECTOR_XPATH)->dragAndDrop($target);
                }
            }
        }
    }
}
