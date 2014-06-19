<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Banner\Test\Block\Adminhtml\Banner;

use Mtf\Client\Element;
use Mtf\Client\Element\Locator;
use Magento\Backend\Test\Block\Widget\Grid as AbstractGrid;

/**
 * Class Grid
 * Gift card account grid block
 */
class Grid extends AbstractGrid
{
    /**
     * Path for types
     *
     * @var string
     */
    protected $typesPath = '//td[contains(@class,"col-banner_types") and contains(.,"%s")]';

    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'banner' => [
            'selector' => '#bannerGrid_banner_filter_banner_name'
        ],
        'visibility' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-store-filter-visible-in"]',
            'input' => 'selectstore',
        ],
        'active' => [
            'selector' => '#bannerGrid_banner_filter_banner_is_enabled',
            'input' => 'select',
        ],
    ];

    /**
     * Check banner in banner grid
     *
     * @param array $filters
     * @param string $type
     * @return bool
     */
    public function isBannerRowVisible($filters, $type = '')
    {
        $result = $this->isRowVisible($filters);
        if ($type) {
            $result = $this->_rootElement->find(sprintf($this->typesPath, $type), Locator::SELECTOR_XPATH)->isVisible();
        }
        return $result;
    }
}
