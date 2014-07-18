<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Page\Adminhtml;

use Mtf\Page\BackendPage;

/**
 * Class IntegrationIndex
 */
class IntegrationIndex extends BackendPage
{
    const MCA = 'admin/integration/index';

    protected $_blocks = [
        'gridPageActions' => [
            'name' => 'gridPageActions',
            'class' => 'Magento\Backend\Test\Block\GridPageActions',
            'locator' => '.page-main-actions',
            'strategy' => 'css selector',
        ],
        'integrationGrid' => [
            'name' => 'integrationGrid',
            'class' => 'Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid',
            'locator' => '#integrationGrid',
            'strategy' => 'css selector',
        ],
        'messagesBlock' => [
            'name' => 'messagesBlock',
            'class' => 'Magento\Core\Test\Block\Messages',
            'locator' => '#messages',
            'strategy' => 'css selector',
        ],
    ];

    /**
     * @return \Magento\Backend\Test\Block\GridPageActions
     */
    public function getGridPageActions()
    {
        return $this->getBlockInstance('gridPageActions');
    }

    /**
     * @return \Magento\Integration\Test\Block\Adminhtml\Integration\IntegrationGrid
     */
    public function getIntegrationGrid()
    {
        return $this->getBlockInstance('integrationGrid');
    }

    /**
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessagesBlock()
    {
        return $this->getBlockInstance('messagesBlock');
    }
}
