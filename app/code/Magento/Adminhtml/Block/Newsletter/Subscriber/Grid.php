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
 * Adminhtml newsletter subscribers grid block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Newsletter\Subscriber;

class Grid extends \Magento\Backend\Block\Widget\Grid
{
    /**
     * Prepare collection for grid
     *
     * @return \Magento\Backend\Block\Widget\Grid
     */
    protected function _prepareCollection()
    {

        if ($this->getRequest()->getParam('queue', false)) {
            $this->getCollection()->useQueue(\Mage::getModel('Magento\Newsletter\Model\Queue')
                ->load($this->getRequest()->getParam('queue')));
        }

        return parent::_prepareCollection();
    }
}
