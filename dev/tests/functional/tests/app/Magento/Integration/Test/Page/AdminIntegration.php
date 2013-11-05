<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Page;

use Magento\Core\Test\Block\Messages;
use Magento\Integration\Test\Block\Backend\IntegrationGrid;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Integrations grid page.
 */
class AdminIntegration extends Page
{
    /**
     * URL for integrations grid page.
     */
    const MCA = 'admin/integration';

    /**
     * Integrations block.
     *
     * @var IntegrationGrid
     */
    private $gridBlock;

    /**
     * Messages block.
     *
     * @var Messages
     */
    private $messageBlock;

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
        $this->gridBlock = Factory::getBlockFactory()->getMagentoIntegrationBackendIntegrationGrid(
            $this->_browser->find('integrationGrid', Locator::SELECTOR_ID));
        $this->messageBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('#messages', Locator::SELECTOR_CSS));
    }

    /**
     * Get integrations grid block.
     *
     * @return IntegrationGrid
     */
    public function getGridBlock()
    {
        return $this->gridBlock;
    }

    /**
     * Get messages block.
     *
     * @return Messages
     */
    public function getMessageBlock()
    {
        return $this->messageBlock;
    }
}
