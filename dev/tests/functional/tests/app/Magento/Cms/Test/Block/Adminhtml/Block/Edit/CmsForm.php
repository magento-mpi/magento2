<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Cms\Test\Block\Adminhtml\Block\Edit;

use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CmsForm
 * Form for Cms Block creation
 */
class CmsForm extends Form
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
    protected $contentForm = '[name="content"]';

    /**
     * Fill the page form
     *
     * @param FixtureInterface $fixture
     * @param Element $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        $this->hideEditor();
        return parent::fill($fixture, $element);
    }

    /**
     * Hide WYSIWYG editor
     *
     * @return void
     */
    protected function hideEditor()
    {
        $content = $this->_rootElement->find($this->contentForm);
        $toggleButton = $this->_rootElement->find($this->toggleButton);
        if (!$content->isVisible() && $toggleButton->isVisible()) {
            $toggleButton->click();
        }
    }
}
