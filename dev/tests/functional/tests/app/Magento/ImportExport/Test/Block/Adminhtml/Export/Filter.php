<?php
/**
 * Created by PhpStorm.
 * User: oonoshko
 * Date: 03.06.14
 * Time: 17:11
 */

namespace Magento\ImportExport\Test\Block\Adminhtml\Export;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;
use Mtf\Client\Element;

/**
 * Class Filter
 */
class Filter extends Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'frontend_label' => [
            'selector' => 'input[name="frontend_label"]'
        ],
        'attribute_code' => [
            'selector' => '[name="attribute_code"]'
        ],
    ];

    /**
     * Checking absence of "attribute" in Filter export grid
     *
     * @param array $filter
     * @param bool $isSearchable
     * @return Element
     */
    public function checkAttributeAbsence(array $filter, $isSearchable = true)
    {
        $message = 'We couldn\'t find any records.';
        if ($isSearchable) {
            $this->search($filter);
        }
        $location = ".//*[contains(@class,'even')]//td[text() = \"" . $message . "\"]";

        return $this->_rootElement->find($location, Locator::SELECTOR_XPATH);
    }
}
