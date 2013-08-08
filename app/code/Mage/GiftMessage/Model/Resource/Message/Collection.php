<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GiftMessage
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Message collection
 *
 * @category    Mage
 * @package     Mage_GiftMessage
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GiftMessage_Model_Resource_Message_Collection extends Magento_Core_Model_Resource_Db_Collection_Abstract
{
    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_GiftMessage_Model_Message', 'Mage_GiftMessage_Model_Resource_Message');
    }
}
