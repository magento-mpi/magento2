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

namespace Magento\Sales\Test\Block\Widget\Guest;

use Mtf\Client\Element\Locator;

/**
 * Orders and Returns form search block
 *
 * @package Magento\Sales\Test\Block\Widget\Guest
 */
class Form extends \Mtf\Block\Form
{
    /**
     * Search button selector
     *
     * @var string
     */
    protected $searchButtonSelector = '.action.submit';

    /**
     * Submit search form
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector, Locator::SELECTOR_CSS)->click();
    }
}
