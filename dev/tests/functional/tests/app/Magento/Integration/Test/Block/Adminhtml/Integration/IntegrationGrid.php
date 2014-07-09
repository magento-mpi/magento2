<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml\Integration;

use Magento\Backend\Test\Block\Widget\Grid;
use Mtf\Client\Element\Locator;

/**
 * Class IntegrationGrid
 * Integrations grid block
 */
class IntegrationGrid extends Grid
{
    /**
     * Initialize block elements
     *
     * @var array
     */
    protected $filters = [
        'name' => [
            'selector' => '#integrationGrid_filter_name',
        ],
        'status' => [
            'selector' => '#integrationGrid_filter_status',
            'input' => 'select',
        ]
    ];

    /**
     * Locator value for edit link
     *
     * @var string
     */
    protected $editLink = 'td[class*=col-edit] > #edit';

    /**
     * Locator value for delete link
     *
     * @var string
     */
    protected $deleteLink = 'td[class*=col-delete] > .delete';

    /**
     * Selector for delete block confirmation window
     *
     * @var string
     */
    protected $deleteBlockSelector = './ancestor::body//div[div[span[text()="Are you sure ?"]]]';

    /**
     * Delete current item
     *
     * @param array $items
     * @return void
     */
    public function delete($items = [])
    {
        $this->search($items);
        $this->_rootElement->find($this->deleteLink)->click();

        /** @var \Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid\DeleteDialog $deleteDialog */
        $deleteDialog = $this->blockFactory->create(
            'Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid\DeleteDialog',
            ['element' => $this->_rootElement->find($this->deleteBlockSelector, Locator::SELECTOR_XPATH)]
        );
        $deleteDialog->acceptDeletion();
    }
}
