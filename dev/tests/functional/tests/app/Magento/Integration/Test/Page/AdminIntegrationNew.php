<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Page;

use Magento\Core\Test\Block\Messages;
use Magento\Integration\Test\Block\Backend\IntegrationForm;
use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * New integration page.
 */
class AdminIntegrationNew extends Page
{
    /**
     * URL for new integration creation page.
     */
    const MCA = 'admin/integration/new';

    /**
     * Integrations block.
     *
     * @var IntegrationForm
     */
    private $integrationFormBlock;

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
        $element = $this->_browser->find('page:main-container', Locator::SELECTOR_ID);
        $this->integrationFormBlock = Factory::getBlockFactory()->getMagentoIntegrationBackendIntegrationForm(
            $element
        );
        $this->messageBlock = Factory::getBlockFactory()->getMagentoCoreMessages(
            $this->_browser->find('messages', Locator::SELECTOR_ID)
        );
    }

    /**
     * Get integration form block.
     *
     * @return IntegrationForm
     */
    public function getIntegrationFormBlock()
    {
        return $this->integrationFormBlock;
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
