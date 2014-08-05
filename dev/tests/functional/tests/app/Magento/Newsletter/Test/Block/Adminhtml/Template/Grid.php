<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Newsletter\Test\Block\Adminhtml\Template;

use Mtf\Client\Element\Locator;

/**
 * Class Grid
 * Newsletter templates grid block
 */
class Grid extends \Magento\Backend\Test\Block\Widget\Grid
{
    /**
     * Filters array mapping
     *
     * @var array
     */
    protected $filters = [
        'code' => [
            'selector' => '[data-ui-id="widget-grid-column-filter-text-1-filter-code"]'
        ]
    ];

    /**
     * Locator for "Action"
     *
     * @var string
     */
    protected $action = '.action-select';

    /**
     * Action for newsletter template
     *
     * @param $action
     * @return void
     */
    public function newsletterTemplateAction($action)
    {
        $this->_rootElement->find($this->action, Locator::SELECTOR_CSS, 'select')->setValue($action);
    }
}
