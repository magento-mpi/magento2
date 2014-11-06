<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Widget\Test\Block\Adminhtml\Widget\Instance\Edit\Tab\LayoutUpdatesType;

use Mtf\Client\Element;
use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Responds for filling layout form
 */
class LayoutForm extends Form
{
    /**
     * Widget option chooser button
     *
     * @var string
     */
    protected $chooser = '//*[@class="chooser_container"]//a/img[contains(@alt,"Open Chooser")]';

    /**
     * Widget option apply button
     *
     * @var string
     */
    protected $apply = '//*[@class="chooser_container"]//a/img[contains(@alt,"Apply")]';

    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Filling layout form
     *
     * @param array $layoutFields
     * @param Element $element
     * @return void
     */
    public function fillForm(array $layoutFields, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($layoutFields);
        foreach ($mapping as $key => $values) {
            $this->_fill([$key => $values], $element);
            $this->getTemplateBlock()->waitLoader();
        }
    }

    /**
     * Getting options data form on the product form
     *
     * @param array $fields
     * @param Element $element
     * @return $this
     */
    public function getDataOptions(array $fields = null, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        return $this->_getData($mapping, $element);
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
