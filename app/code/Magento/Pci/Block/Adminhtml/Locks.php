<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Locked administrators page
 *
 */
namespace Magento\Pci\Block\Adminhtml;

class Locks extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->buttonList->remove('add');
    }
}
