<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_SalesArchive
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 *  Add sales archiving to order's grid view massaction
 *  @deprecated
 */
namespace Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid;

class Massaction extends \Magento\Adminhtml\Block\Widget\Grid\Massaction\AbstractMassaction
{
    /**
     * Before rendering html operations
     *
     * @return \Magento\SalesArchive\Block\Adminhtml\Sales\Order\Grid\Massaction
     */
    protected function _beforeToHtml()
    {
        $isActive = \Mage::getSingleton('Magento\SalesArchive\Model\Config')->isArchiveActive();
        if ($isActive && $this->_authorization->isAllowed('Magento_SalesArchive::add')) {
            $this->addItem('add_order_to_archive', array(
                 'label'=> __('Move to Archive'),
                 'url'  => $this->getUrl('*/sales_archive/massAdd'),
            ));
        }
        return parent::_beforeToHtml();
    }
}
