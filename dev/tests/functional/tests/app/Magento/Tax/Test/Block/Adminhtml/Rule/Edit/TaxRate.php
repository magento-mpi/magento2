<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Test\Block\Adminhtml\Rule\Edit;

use Mtf\Block\Form as FormInterface;

/**
 * Class TaxRate
 * Tax rate block
 *
 * @package Magento\Tax\Test\Block\Adminhtml\Rule\Edit
 */
class TaxRate extends FormInterface
{
    /**
     * 'Save' button on dialog window for creating new tax rate
     *
     * @var string
     */
    protected $saveTaxRate = '#tax-rule-edit-apply-button';

    /**
     * Clicking 'Save' button on dialog window for creating new tax rate
     */
    public function saveTaxRate()
    {
        $this->_rootElement->find($this->saveTaxRate)->click();
    }
}
