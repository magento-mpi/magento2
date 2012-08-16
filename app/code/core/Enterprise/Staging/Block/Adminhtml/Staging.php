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
 * Staging manage stagings block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Staging extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Enterprise_Staging';
        $this->_controller = 'adminhtml_staging';
        $this->_headerText = Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Websites');
        $this->_addButtonLabel = Mage::helper('Enterprise_Staging_Helper_Data')->__('Add Staging Website');
        parent::_construct();
    }

    public function getHeaderCssClass() {
        return 'icon-head head-staging';
    }
}
