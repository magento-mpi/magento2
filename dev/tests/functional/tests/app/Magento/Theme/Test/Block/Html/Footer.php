<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Theme\Test\Block\Html;

use Mtf\Block\Block;
use Mtf\Client\Element\Locator;

/**
 * Footer block
 */
class Footer extends Block
{
    /**
     * Link selector
     *
     * @var string
     */
    protected $linkSelector = '//*[contains(@class, "links")]//a[contains(text(), "%s")]';

    /**
     * Variable selector
     *
     * @var string
     */
    protected $variableSelector = './/div[contains(@class, "links")]/*[text()="%s"]';

    /**
     * Click on link by name
     *
     * @param string $linkName
     * @return \Mtf\Client\Element
     * @throws \Exception
     */
    public function clickLink($linkName)
    {
        $link = $this->_rootElement->find(sprintf($this->linkSelector, $linkName), Locator::SELECTOR_XPATH);
        if (!$link->isVisible()) {
            throw new \Exception(sprintf('"%s" link is not visible', $linkName));
        }
        $link->click();
    }

    /**
     * Check Variable visibility by html value
     *
     * @param string $htmlValue
     * @return bool
     */
    public function checkVariable($htmlValue)
    {
        return $this->_rootElement->find(sprintf($this->variableSelector, $htmlValue), Locator::SELECTOR_XPATH)->isVisible();
    }
}
