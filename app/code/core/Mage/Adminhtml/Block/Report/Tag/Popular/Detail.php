<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tag detail report blocks content block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Report_Tag_Popular_Detail extends Mage_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'report_tag_popular_detail';

        $tag = Mage::getModel('Mage_Tag_Model_Tag')->load($this->getRequest()->getParam('id'));

        $this->_headerText = Mage::helper('Mage_Reports_Helper_Data')->__('Tag "%s" details', $this->escapeHtml($tag->getName()));
        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/popular/'));
        $this->_addBackButton();
    }

}
