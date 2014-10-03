<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Order;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Class View
 * Order view block on view order page
 */
class View extends Block
{
    /**
     * Link xpath selector
     *
     * @var string
     */
    protected $link = '//*[contains(@class,"order-links")]//a[normalize-space(.)="%s"]';

    /**
     * Open link by name
     *
     * @param string $name
     * @return void
     */
    public function openLinkByName($name)
    {
        $this->_rootElement->find(sprintf($this->link, $name), Locator::SELECTOR_XPATH)->click();
    }
}
