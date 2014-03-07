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

use Mtf\Fixture\FixtureInterface;
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
    protected $taxClassRow = './/label[input[contains(@class, "mselect-checked")]]';

    /**
     * Add new tax class button
     *
     * @var string
     */
    protected $addNewTaxClass = '.action-add';

    /**
     * Tax class to select
     *
     * @var string
     */
    protected $taxClassItem = './/*[contains(@class, "mselect-list-item")]//span';

    /**
     * New tax class input field
     *
     * @var string
     */
    protected $newTaxClass = '.mselect-input';

    /**
     * Save new tax class
     *
     * @var string
     */
    protected $saveTaxClass = '.mselect-save';

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
