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
class Api extends Tab
{
    /**
     * {@inheritdoc}
     */
    protected function _init()
    {
        parent::_init();
        $this->_mapping = array(
            'enable_api_access' => '#enable_api_access_checkbox',
            'resource_access' => '#all',
        );
    }
}
