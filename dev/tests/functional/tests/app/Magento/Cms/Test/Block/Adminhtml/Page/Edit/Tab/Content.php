<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Content
 * Backend cms page content tab
 */
class Content extends Tab
{
    /**
     * System Variable block selector
     *
     * @var string
     */
    protected $systemVariableBlock = "./ancestor::body//div[div[@id='variables-chooser']]";

    /**
     * Widget block selector
     *
     * @var string
     */
    protected $widgetBlock = "./ancestor::body/div[div/div/*[@id='widget_options_form']]";

    /**
     * Insert Variable button selector
     *
     * @var string
     */
    protected $addVariableButton = ".add-variable";

    /**
     * Insert Widget button selector
     *
     * @var string
     */
    protected $addWidgetButton = '.action-add-widget';

    /**
     * Content input locator
     *
     * @var string
     */
    protected $content = '#page_content';

    /**
     * Content Heading input locator
     *
     * @var string
     */
    protected $contentHeading = '#page_content_heading';

    /**
     * Clicking in content tab 'Insert Variable' button
     *
     * @return void
     */
    public function clickInsertVariable()
    {
        $addVariableButton = $this->_rootElement->find($this->addVariableButton);
        if ($addVariableButton->isVisible()) {
            $addVariableButton->click();
        }
    }

    /**
     * Clicking in content tab 'Insert Widget' button
     *
     * @return void
     */
    public function clickInsertWidget()
    {
        $addWidgetButton = $this->_rootElement->find($this->addWidgetButton);
        if ($addWidgetButton->isVisible()) {
            $addWidgetButton->click();
        }
    }

    /**
     * Get for wysiwyg config block
     *
     * @return \Magento\Cms\Test\Block\Adminhtml\Wysiwyg\Config
     */
    public function getWysiwygConfig()
    {
        $config = $this->blockFactory->create(
            'Magento\Cms\Test\Block\Adminhtml\Wysiwyg\Config',
            ['element' => $this->_rootElement->find($this->systemVariableBlock, Locator::SELECTOR_XPATH)]
        );

        return $config;
    }

    /**
     * Get widget block
     *
     * @return \Magento\Widget\Test\Block\Adminhtml\WidgetForm
     */
    public function getWidgetBlock()
    {
        $widgetBlock = $this->blockFactory->create(
            'Magento\Widget\Test\Block\Adminhtml\WidgetForm',
            ['element' => $this->_rootElement->find($this->widgetBlock, Locator::SELECTOR_XPATH)]
        );

        return $widgetBlock;
    }

    /**
     * Fill data to content fields on content tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        $element->find($this->content)->setValue($fields['content']['value']['content']);
        if (isset($fields['content_heading']['value'])) {
            $element->find($this->contentHeading)->setValue($fields['content_heading']['value']);
        }
        if (isset($fields['content']['value']['widget']['preset'])) {
            foreach ($fields['content']['value']['widget']['preset'] as $widget) {
                $this->clickInsertWidget();
                $this->getWidgetBlock()->addWidget($widget);
            }
        }
        if (isset($fields['content']['value']['variable'])) {
            $this->clickInsertVariable();
            $config = $this->getWysiwygConfig();
            $config->selectVariableByName($fields['content']['value']['variable']);
        }

        return $this;
    }

    /**
     * Get data of content tab
     *
     * @param array|null $fields
     * @param Element|null $element
     * @return array
     */
    public function getDataFormTab($fields = null, Element $element = null)
    {
        return [
            'content' => [],
            'content_heading' => ''
        ];
    }
}
