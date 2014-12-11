<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Block\Adminhtml\Wysiwyg;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class Config
 * System variable management block
 */
class Config extends Block
{
    /**
     * Selector for getting all variables in list
     *
     * @var string
     */
    protected $variablesSelector = '.insert-variable > li > a';

    /**
     * Variable link selector
     *
     * @var string
     */
    protected $variableSelector = '//*[@class="insert-variable"]//a[contains(text(),"%s")]';

    /**
     * Returns array with all variables
     *
     * @return array
     */
    public function getAllVariables()
    {
        $values = [];

        $variableElements = $this->_rootElement->find($this->variablesSelector)->getElements();
        foreach ($variableElements as $variableElement) {
            if ($variableElement->isVisible()) {
                $values[] = $variableElement->getText();
            }
        }

        return $values;
    }

    /**
     * Select variable by name
     *
     * @param string $variableName
     * @return void
     */
    public function selectVariableByName($variableName)
    {
        $this->_rootElement->find(sprintf($this->variableSelector, $variableName), Locator::SELECTOR_XPATH)->click();
    }
}
