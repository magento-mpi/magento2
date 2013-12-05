<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Test\Block\Adminhtml;

use Magento\Backend\Test\Block\Widget\Grid;

/**
 * Integrations grid block.
 */
class IntegrationGrid extends Grid
{
    /**
     * Initialize block elements
     */
    protected function _init()
    {
        parent::_init();
        $this->filters = array(
            'name' => array(
                'selector' => '#integrationGrid_filter_name'
            ),
            'status' => array(
                'selector' => '#integrationGrid_filter_status',
                'input' => 'select'
            ),
        );
        $this->editLink = '//button[@id="edit"]';
    }
}
