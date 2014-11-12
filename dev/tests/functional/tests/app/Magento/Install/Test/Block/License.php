<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Install\Test\Block;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * License block.
 */
class License extends Block
{
    /**
     * 'Back' button.
     *
     * @var string
     */
    protected $back = "//*[.=' Go Back']";

    /**
     * License text.
     *
     * @var string
     */
    protected $license = '//*[@class="col-xs-9"]';

    /**
     * Click on 'Back' button.
     *
     * @return void
     */
    public function clickBack()
    {
        $this->_rootElement->find($this->back, Locator::SELECTOR_XPATH)->click();
    }

    /**
     * Get license text.
     *
     * @return string
     */
    public function getLicense()
    {
        return $this->_rootElement->find($this->license, Locator::SELECTOR_XPATH)->getText();
    }
}