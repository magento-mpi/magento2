<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Backend\IntegrationForm;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Integration tab of integration edit page.
 */
class IntegrationTab extends Tab
{
    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        parent::_init();
        $this->_mapping = array(
            'name' => '#integration_properties_name',
            'email' => '#integration_properties_email',
            'authentication' => '#integration_properties_authentication',
            'endpoint' => '#integration_properties_endpoint',
        );
    }
}
