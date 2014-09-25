<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Tab;
use Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\LayoutForm;

/**
 * Class LayoutUpdates
 * Layout Updates form
 */
class LayoutUpdates extends Tab
{
    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Form selector
     *
     * @var string
     */
    // @codingStandardsIgnoreStart
    protected $formSelector = '(//*[@class="fieldset-wrapper-content"]//div[contains(@class,"group_container") and not(contains(@class,"no-display"))])[last()]';
    // @codingStandardsIgnoreEnd

    /**
     * 'Add Option' button
     *
     * @var string
     */
    protected $addLayoutUpdates = '[data-ui-id="widget-button"]';

    /**
     * Page group locator
     *
     * @var string
     */
    protected $pageGroup = '[id=page_group_container_%d] select[name$="[page_group]"]';

    /**
     * Fill Layout Updates form
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        foreach ($fields['layout']['value'] as $key => $field) {
            $this->addLayoutUpdates();
            $this->_rootElement->find(sprintf($this->pageGroup, $key), Locator::SELECTOR_CSS, 'select')
                ->setValue($field['page_group']);
            $this->getTemplateBlock()->waitLoader();

            /** @var LayoutForm $layoutForm */
            $layoutForm = $this->blockFactory->create(
                'Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType\\' . $this
                    ->optionNameConvert($field['page_group']),
                [
                    'element' => $this->_rootElement->find($this->formSelector, Locator::SELECTOR_XPATH)
                ]
            );
            $layoutForm->fillForm($field);
        }
        return $this;
    }

    /**
     * Click Add Layout Updates button
     *
     * @return void
     */
    protected function addLayoutUpdates()
    {
        $this->_rootElement->find($this->addLayoutUpdates)->click();
    }

    /**
     * Prepare class name
     *
     * @param string $pageGroup
     * @return string
     */
    protected function optionNameConvert($pageGroup)
    {
        $names = explode(' ', $pageGroup);
        foreach ($names as $name) {
            if ($name == 'Categories' || $name == 'Product' || $name == 'Gift' || $name == 'Page' || $name == 'Pages') {
                if ($name == 'Gift' || $name == 'Page' || $name == 'Pages') {
                    return $name = 'Page';
                }
                return $name;
            }
        }
    }

    /**
     * Get backend abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    protected function getTemplateBlock()
    {
        return $this->blockFactory->create(
            'Magento\Backend\Test\Block\Template',
            ['element' => $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)]
        );
    }
}
