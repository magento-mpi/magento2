<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cms\Block\Adminhtml\Page\Edit;

/**
 * Admin page left menu
 *
 * @category   Magento
 * @package    Magento_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('page_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Page Information'));
    }
}
