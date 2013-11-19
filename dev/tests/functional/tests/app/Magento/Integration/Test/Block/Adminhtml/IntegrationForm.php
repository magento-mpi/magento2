<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\FormTabs;
use Mtf\Client\Element\Locator;

/**
 * Integration form block.
 */
class IntegrationForm extends FormTabs
{
    protected $apiTab = '#integration_edit_tabs_api_section';
    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        parent::_init();
        $this->_tabClasses = array(
            'integration_edit_tabs_info_section' =>
                '\\Magento\\Integration\\Test\\Block\\Adminhtml\\Integration\\Edit\\Tab\\Info',
            'integration_edit_tabs_api_section' =>
                '\\Magento\\Integration\\Test\\Block\\Adminhtml\\Integration\\Edit\\Tab\\Api',
        );
    }

    public function openApiTab()
    {
        $this->_rootElement->find($this->apiTab, Locator::SELECTOR_CSS)->click();
    }
}
