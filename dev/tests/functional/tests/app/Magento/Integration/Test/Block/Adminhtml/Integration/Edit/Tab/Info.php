<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml\Integration\Edit\Tab;

use Magento\Backend\Test\Block\Widget\Tab;

/**
 * Integration tab of integration edit page.
 */
class Info extends Tab
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
            'callback_url' => '#integration_properties_endpoint',
        );
    }
}
