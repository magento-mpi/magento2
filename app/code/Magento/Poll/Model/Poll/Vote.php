<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Poll
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Pool vote model
 *
 * @method Magento_Poll_Model_Resource_Poll_Vote _getResource()
 * @method Magento_Poll_Model_Resource_Poll_Vote getResource()
 * @method int getPollId()
 * @method Magento_Poll_Model_Poll_Vote setPollId(int $value)
 * @method int getPollAnswerId()
 * @method Magento_Poll_Model_Poll_Vote setPollAnswerId(int $value)
 * @method int getIpAddress()
 * @method Magento_Poll_Model_Poll_Vote setIpAddress(int $value)
 * @method int getCustomerId()
 * @method Magento_Poll_Model_Poll_Vote setCustomerId(int $value)
 * @method string getVoteTime()
 * @method Magento_Poll_Model_Poll_Vote setVoteTime(string $value)
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Poll_Model_Poll_Vote extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Poll_Model_Resource_Poll_Vote');
    }

    /**
     * Processing object before save data
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        if (!$this->getVoteTime()) {
            $this->setVoteTime(Mage::getSingleton('Magento_Core_Model_Date')->gmtDate());
        }
        return parent::_beforeSave();
    }
}
