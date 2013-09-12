<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Gift Wrapping Adminhtml Block
 * 
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_GiftWrapping_Block_Adminhtml_Giftwrapping extends Magento_Backend_Block_Widget_Grid_Container
{
    /**
     * Initialize gift wrapping management page
     *
     * @return void
     */
    public function _construct()
    {
        $this->_controller = 'adminhtml_giftwrapping';
        $this->_blockGroup = 'Magento_GiftWrapping';
        $this->_headerText = __('Gift Wrapping');
        $this->_addButtonLabel = __('Add Gift Wrapping');
        parent::_construct();
    }
}
