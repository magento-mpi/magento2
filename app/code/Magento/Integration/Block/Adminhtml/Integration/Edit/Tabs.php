<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Integration
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Integration\Edit;

class Tabs extends \Magento\Adminhtml\Block\Widget\Tabs
{
    /**
     * Initialize integration edit page tabs
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('integration_info_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Integration Information'));
    }
}
