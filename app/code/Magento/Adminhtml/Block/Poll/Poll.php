<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Poll manager grid
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Poll_Poll extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'poll';
        $this->_headerText = __('Poll Manager');
        $this->_addButtonLabel = __('Add New Poll');
        parent::_construct();
    }

}
