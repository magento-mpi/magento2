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
 * Order create errors block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Sales_Order_Create_Messages extends Mage_Core_Block_Messages
{

    public function _prepareLayout()
    {
        $this->addMessages(Mage::getSingleton('Mage_Adminhtml_Model_Session_Quote')->getMessages(true));
        parent::_prepareLayout();
    }

}
