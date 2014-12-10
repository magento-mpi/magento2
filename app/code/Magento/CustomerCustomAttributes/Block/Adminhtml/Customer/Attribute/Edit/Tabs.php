<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Customer attributes edit page tabs
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerCustomAttributes\Block\Adminhtml\Customer\Attribute\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Initialize edit tabs
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->setId('customer_attribute_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Attribute Information'));
    }
}
