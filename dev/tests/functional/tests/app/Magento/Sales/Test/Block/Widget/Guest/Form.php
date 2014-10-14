<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Widget\Guest;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Mtf\Fixture\FixtureInterface;

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
     * Selector for loads form
     *
     * @var string
     */
    protected $loadsForm = 'div[id*=oar] input';

    /**
     * Submit search form
     */
    public function submit()
    {
        $this->_rootElement->find($this->searchButtonSelector, Locator::SELECTOR_CSS)->click();
    }

    /**
     * Fill the root form
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $this->waitLoadForm();
        parent::fill($fixture, $element);
    }

    /**
     * @return void
     */
    protected function waitLoadForm()
    {
        $browser = $this->browser;
        $selector = $this->loadsForm;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $inputs = $browser->find($selector)->getElements();
                $i = 0;
                foreach ($inputs as $input) {
                    if ($input->isVisible()) {
                        ++$i;
                    }
                }
                return $i == 1 ? true : null;
            }
        );
    }
}
