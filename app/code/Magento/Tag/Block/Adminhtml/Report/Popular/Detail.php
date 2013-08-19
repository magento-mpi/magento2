<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tag
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tag detail report blocks content block
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tag_Block_Adminhtml_Report_Popular_Detail extends Magento_Backend_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'Magento_Tag';
        $this->_controller = 'adminhtml_report_popular_detail';

        $tag = Mage::getModel('Magento_Tag_Model_Tag')->load($this->getRequest()->getParam('id'));

        $this->_headerText = __('Tag "%1" details', $this->escapeHtml($tag->getName()));
        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_tag/popular/'));
        $this->_addBackButton();
    }
}
