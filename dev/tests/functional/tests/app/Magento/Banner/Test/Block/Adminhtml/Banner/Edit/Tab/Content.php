<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Test\Block\Adminhtml\Banner\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Content
 * Banner content per store view edit page
 */
class Content extends Tab
{
    /**
     * Use banner content selector
     *
     * @var string
     */
    protected $contentsNotUse = '[name="store_contents_not_use[%s]"]';

    /**
     * Banner content selector
     *
     * @var string
     */
    protected $storeContent = '[name="store_contents[%s]"]';

    /**
     * Fill data to content fields on content tab
     *
     * @param array $fields
     * @param Element|null $element
     * @return $this
     */
    public function fillFormTab(array $fields, Element $element = null)
    {
        if (isset($fields['store_contents_not_use'])) {
            foreach ($fields['store_contents_not_use']['value'] as $key => $storeContentUse) {
                $store = explode('_', $key);
                $element->find(sprintf($this->contentsNotUse, $store[1]), Locator::SELECTOR_CSS, 'checkbox')
                    ->setValue($storeContentUse);
            }
        }
        if (isset($fields['store_contents'])) {
            foreach ($fields['store_contents']['value'] as $key => $storeContent) {
                $store = explode('_', $key);
                if ($storeContent != "-") {
                    $element->find(sprintf($this->storeContent, $store[1]))->setValue($storeContent);
                }
            }
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
        $storeContent = [];
        $count = 0;
        $field = $this->_rootElement->find(sprintf($this->storeContent, $count), Locator::SELECTOR_CSS, 'checkbox');
        while ($field->isVisible()) {
            $fieldValue = $field->getValue();
            if ($fieldValue != '') {
                $storeContent[$count] = $fieldValue;
            }
            ++$count;
            $field = $this->_rootElement->find(sprintf($this->storeContent, $count), Locator::SELECTOR_CSS, 'checkbox');
        }

        $storeContentUse = [];
        $count = 0;
        $field = $this->_rootElement->find(sprintf($this->contentsNotUse, $count));
        while ($field->isVisible()) {
            $fieldValue = $field->getValue();
            if ($fieldValue != '') {
                $storeContentUse[$count] = $fieldValue;
            }
            ++$count;
            $field = $this->_rootElement->find(sprintf($this->contentsNotUse, $count));
        }

        return ['store_content' => $storeContent, 'store_contents_not_use' => $storeContentUse];
    }
}
