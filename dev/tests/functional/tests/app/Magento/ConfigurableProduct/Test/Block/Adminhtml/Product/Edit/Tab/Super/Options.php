<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\ConfigurableProduct\Test\Block\Adminhtml\Product\Edit\Tab\Super;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Catalog\Test\Block\Adminhtml\Product\Edit\Tab\Options as CatalogOptions;

/**
 * Class Options
 * Attribute options row form
 */
class Options extends CatalogOptions
{
    /**
     * CSS selector name item
     *
     * @var string
     */
    protected $nameSelector = 'td[data-column="name"]';

    /**
     * XPath selector percent label
     *
     * @var string
     */
    protected $percentSelector = '//button[span[contains(text(),"%")]]';

    /**
     * Getting options data form on the product form
     *
     * @param array|null $fields [optional]
     * @param Element|null $element [optional]
     * @return array
     */
    public function getDataOptions(array $fields = null, Element $element = null)
    {
        $element = $element === null ? $this->_rootElement : $element;
        $mapping = $this->dataMapping($fields);
        $data = $this->_getData($mapping, $element);

        $data['is_percent'] = 'No';
        $percentElement = $element->find($this->percentSelector, Locator::SELECTOR_XPATH);
        if ($percentElement->isVisible()) {
            $data['is_percent'] = 'Yes';
        }

        $nameElement = $element->find($this->nameSelector);
        if ($nameElement->isVisible()) {
            $data['name'] = $nameElement->getText();
        }

        return $data;
    }
}
