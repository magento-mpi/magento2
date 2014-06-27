<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Block\Edit;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;
use Mtf\Block\Form as ParentForm;
use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Form for Cms Block creation
 */
class Form extends ParentForm
{
    /**
     * Content Editor toggle button id
     *
     * @var string
     */
    protected $toggleButton = "#toggleblock_content";

    /**
     * Fill the page form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $this->toggleEditor();
        return parent::fill($fixture);
    }

    /**
     * Block content Show/Hide Editor toggle button
     *
     * @return void
     */
    protected function toggleEditor()
    {
        $toggleButton = $this->_rootElement->find($this->toggleButton, Locator::SELECTOR_CSS);
        if ($toggleButton->isVisible()) {
            $toggleButton->click();
        }
    }
}
