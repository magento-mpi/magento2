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
     * @var string
     */
    protected $integrationFormBlock = 'page:main-container';

    /**
     * Messages block.
     *
     * @var string
     */
    protected $messageBlock = 'messages';

    /**
     * Api tab of integration edit page.
     *
     * @var string
     */
    protected $apiBlock = '#integration_edit_tabs_api_section_content';

    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        $this->_url = $_ENV['app_backend_url'] . self::MCA;
    }

    /**
     * Get integration form block.
     *
     * @return \Magento\Integration\Test\Block\Adminhtml\IntegrationForm
     */
    public function getIntegrationFormBlock()
    {
        return  Factory::getBlockFactory()->getMagentoIntegrationAdminhtmlIntegrationForm(
            $this->_browser->find($this->integrationFormBlock, Locator::SELECTOR_ID)
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
            $this->_browser->find($this->messageBlock, Locator::SELECTOR_ID)
        );
    }

    /**
     * Get api tab of integration edit page.
     *
     * @return \Magento\Integration\Test\Block\Adminhtml\Integration\Edit\Tab\Api
     */
    public function getApiTab()
    {
        return Factory::getBlockFactory()->getMagentoIntegrationAdminhtmlIntegrationEditTabApi(
            $this->_browser->find($this->apiBlock, Locator::SELECTOR_CSS)
        );
    }
}
