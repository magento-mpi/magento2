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
            'selector' => 'input[name="name"]',
        ],
        'status' => [
            'selector' => 'input[name="status"]',
            'input' => 'select',
        ]
    ];

    /**
     * Locator value for link in action column
     *
     * @var string
     */
    protected $editLink = '[data-column="edit"] button';

    /**
     * Locator value for delete link
     *
     * @var string
     */
    protected $deleteLink = '[data-column="delete"] button';

    /**
     * Selector for delete block confirmation window
     *
     * @var string
     */
    protected $deleteBlockSelector = './/ancestor::body/div[div[@id="integration-delete-container"]]';

    /**
     * Search and delete current item
     *
     * @param array $item
     * @return void
     */
    public function searchAndDelete(array $item)
    {
        $this->search($item);
        $this->_rootElement->find($this->deleteLink)->click();

        /** @var \Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid\DeleteDialog $deleteDialog */
        $deleteDialog = $this->blockFactory->create(
            'Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid\DeleteDialog',
            ['element' => $this->_rootElement->find($this->deleteBlockSelector, Locator::SELECTOR_XPATH)]
        );
        $deleteDialog->acceptDeletion();
    }
}
