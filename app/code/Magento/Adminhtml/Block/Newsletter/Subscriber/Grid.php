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
class Magento_Adminhtml_Block_Newsletter_Subscriber_Grid extends Magento_Backend_Block_Widget_Grid
{
    /**
     * Prepare collection for grid
     *
     * @return Magento_Backend_Block_Widget_Grid
     */
    protected function _prepareCollection()
    {

        if ($this->getRequest()->getParam('queue', false)) {
            $this->getCollection()->useQueue(Mage::getModel('Magento_Newsletter_Model_Queue')
                ->load($this->getRequest()->getParam('queue')));
        }

        return parent::_prepareCollection();
    }
}
