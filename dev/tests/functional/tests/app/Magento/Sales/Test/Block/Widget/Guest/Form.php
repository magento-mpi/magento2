<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Widget\Guest;

use Mtf\Client\Element\Locator;

/**
 * Orders and Returns form search block
 *
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
