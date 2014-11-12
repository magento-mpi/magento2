<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Block;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Web configuration block.
 */
class WebConfiguration extends Form
{
    /**
     * 'Next' button.
     *
     * @var string
     */
    protected $next = "//*[.='Next']";

    /**
     * Click on 'Next' button.
     *
     * @return void
     */
    public function clickNext()
    {
        $this->_rootElement->find($this->next, Locator::SELECTOR_XPATH)->click();
    }
}