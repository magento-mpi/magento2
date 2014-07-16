<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
     * Variable link selector
     *
     * @var string
     */
    protected $variableSelector = '//a[contains(text(),"%s")]';

    /**
     * Selector for getting all variables in list
     *
     * @var string
     */
    protected $variablesSelector = '.insert-variable > li > a';

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
