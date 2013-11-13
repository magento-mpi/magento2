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

namespace Magento\Tax\Test\Block\Adminhtml\Rule\Edit;

use Mtf\Fixture;
use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class TaxClass
 * Customer/Product Tax Classes block
 *
 * @package Magento\Tax\Test\Block\Adminhtml\Rule\Edit
 */
class TaxClass extends Block
{
    /**
     * Tax class row item
     *
     * @var string
     */
    private $taxClassRow;

    /**
     * Add new tax class button
     *
     * @var string
     */
    private $addNewTaxClass;

    /**
     * Tax class to select
     *
     * @var string
     */
    private $taxClassItem;

    /**
     * New tax class input field
     *
     * @var string
     */
    private $newTaxClass;

    /**
     * Save new tax class
     *
     * @var string
     */
    private $saveTaxClass;

    /**
     * Initialize elements in block
     */
    protected function _init()
    {
        $this->taxClassRow = './/*[contains(@class, "mselect-list-item")]'
            .' //label[input[contains(@class, "mselect-checked")]]';
        $this->addNewTaxClass = '.action-add';
        $this->taxClassItem = './/*[contains(@class, "mselect-list-item")]//span';
        $this->newTaxClass = '.mselect-input';
        $this->saveTaxClass = '.mselect-save';
    }

    /**
     * Select Tax Class in multiselect and create new one if required
     *
     * @param array $taxClasses
     */
    public function selectTaxClass($taxClasses)
    {
        //Uncheck all marked classes
        while ($this->_rootElement->find($this->taxClassRow, Locator::SELECTOR_XPATH)->isVisible()) {
            $this->_rootElement->find($this->taxClassRow, Locator::SELECTOR_XPATH)->click();
        }
        //Select tax classes
        foreach ($taxClasses as $class) {
            $taxOption = $this->_rootElement->find($this->taxClassItem . '[text()="' . $class . '"]',
                Locator::SELECTOR_XPATH);
            if (!$taxOption->isVisible()) {
                $this->_rootElement->find($this->addNewTaxClass, Locator::SELECTOR_CSS)->click();
                $this->_rootElement->find($this->newTaxClass, Locator::SELECTOR_CSS)->setValue($class);
                $this->_rootElement->find($this->saveTaxClass, Locator::SELECTOR_CSS)->click();
                $this->waitForElementVisible($this->taxClassRow . '/span[text()="' . $class . '"]',
                    Locator::SELECTOR_XPATH);
            } else {
                $this->_rootElement->find('//label/span[text()="' . $class . '"]', Locator::SELECTOR_XPATH)->click();
            }
        }
    }
}
