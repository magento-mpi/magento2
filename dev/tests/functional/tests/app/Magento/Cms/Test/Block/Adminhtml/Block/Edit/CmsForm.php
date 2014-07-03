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
 * Class CmsForm
 * Form for Cms Block creation
 */
class CmsForm extends ParentForm
{
    /**
     * Content Editor toggle button id
     *
     * @var string
     */
    protected $toggleButton = "#toggleblock_content";

    /**
     * CMS Block Content area
     *
     * @var string
     */
    protected $contentForm = "#block_content";

    /**
     * Fill the page form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $this->showEditor();
        return parent::fill($fixture);
    }

    /**
     * Block Content Show/Hide Editor toggle button
     *
     * @return void
     */
    protected function showEditor()
    {
        $content = $this->_rootElement->find($this->contentForm);
        $toggleButton = $this->_rootElement->find($this->toggleButton);
        if (!$content->isVisible() && $toggleButton->isVisible()) {
            $toggleButton->click();
        }
    }
}
