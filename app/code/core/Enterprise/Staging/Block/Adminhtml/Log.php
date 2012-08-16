<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging log history
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Log extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_Staging';
        $this->_controller = 'adminhtml_log';
        $this->_headerText = Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Operations Log');
        parent::_construct();

        $this->_removeButton('add');
    }

    public function getHeaderCssClass() {
        return 'icon-head head-staging-log';
    }
}
