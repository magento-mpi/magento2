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
 * Adminhtml report review product blocks content block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Report\Review;

class Detail extends \Magento\Adminhtml\Block\Widget\Grid\Container
{

    protected function _construct()
    {
        $this->_controller = 'report_review_detail';

        $product = \Mage::getModel('Magento\Catalog\Model\Product')->load($this->getRequest()->getParam('id'));
        $this->_headerText = __('Reviews for %1', $product->getName());

        parent::_construct();
        $this->_removeButton('add');
        $this->setBackUrl($this->getUrl('*/report_review/product/'));
        $this->_addBackButton();
    }

}
