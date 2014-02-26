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

namespace Magento\Review\Test\Block;

use Mtf\Block\Form as BlockForm;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Review form
 *
 * @package Magento\Review\Test\Block
 */
class Form extends BlockForm
{
    /**
     * 'Submit' review button selector
     *
     * @var string
     */
    protected $submitButton = '.action.submit';

    /**
     * Submit review form
     */
    public function submit()
    {
        $this->_rootElement->find($this->submitButton, Locator::SELECTOR_CSS)->click();
    }
}
