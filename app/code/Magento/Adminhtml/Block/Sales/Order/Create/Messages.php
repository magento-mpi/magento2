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
 * Order create errors block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Messages extends Magento_Adminhtml_Block_Messages
{

    protected function _prepareLayout()
    {
        $this->addMessages(Mage::getSingleton('Magento_Adminhtml_Model_Session_Quote')->getMessages(true));
        parent::_prepareLayout();
    }

}
