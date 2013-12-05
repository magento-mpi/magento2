<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Page;

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
     * @var string
     */
    protected $gridBlock = '#integrationGrid';

    /**
     * Messages block.
     *
     * @var string
     */
    protected $messageBlock = '#messages';

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get integrations grid block.
     *
     * @return \Magento\Integration\Test\Block\Adminhtml\IntegrationGrid
     */
    public function getGridBlock()
    {
        return  Factory::getBlockFactory()->getMagentoIntegrationAdminhtmlIntegrationGrid(
            $this->_browser->find($this->gridBlock, Locator::SELECTOR_CSS)
        );
    }

    /**
     * Get messages block.
     *
     * @return \Magento\Core\Test\Block\Messages
     */
    public function getMessageBlock()
    {
        return Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find($this->messageBlock, Locator::SELECTOR_CSS)
        );
    }
}
