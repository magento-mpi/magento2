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
 * Options form
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
    protected $draggableColumn = './ancestor::body//td[@class="col-draggable"]';

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
        foreach ($fields['options']['value'] as $field) {
            $this->_rootElement->find($this->addOption)->click();
            $this->blockFactory->create(
                'Magento\CustomerCustomAttributes\Test\Block\Adminhtml\Customer\Attribute\Edit\Tab\Options\Option',
                ['element' => $this->_rootElement->find('.ui-sortable tr:last-child')]
            )->fillOptions($field);
        }

        $elements = $this->_rootElement->find($this->optionSelector, Locator::SELECTOR_CSS)->getElements();

        // dragAndDrop options according to order from fixture
        foreach ($fields['options']['value'] as $option) {
            if ($option['order'] > count($elements)) {
                throw new \Exception("Order number of options from fixture is greater than form options number.");
            }
            $target = $this->_rootElement->find(
                sprintf($this->targetElement, $option['order']),
                Locator::SELECTOR_XPATH
            );
            foreach ($elements as $element) {
                if ($element->getValue() == $option['admin']) {
                    $element->find($this->draggableColumn, Locator::SELECTOR_XPATH)->dragAndDrop($target);
                }
            }
        }

        return $this;
    }
}
