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
 * @method \Magento\Poll\Model\Resource\Poll\Vote _getResource()
 * @method \Magento\Poll\Model\Resource\Poll\Vote getResource()
 * @method int getPollId()
 * @method \Magento\Poll\Model\Poll\Vote setPollId(int $value)
 * @method int getPollAnswerId()
 * @method \Magento\Poll\Model\Poll\Vote setPollAnswerId(int $value)
 * @method int getIpAddress()
 * @method \Magento\Poll\Model\Poll\Vote setIpAddress(int $value)
 * @method int getCustomerId()
 * @method \Magento\Poll\Model\Poll\Vote setCustomerId(int $value)
 * @method string getVoteTime()
 * @method \Magento\Poll\Model\Poll\Vote setVoteTime(string $value)
 *
 * @category    Magento
 * @package     Magento_Poll
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Poll\Model\Poll;

class Vote extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magento\Poll\Model\Resource\Poll\Vote');
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        if (!$this->getVoteTime()) {
            $this->setVoteTime(\Mage::getSingleton('Magento\Core\Model\Date')->gmtDate());
        }
        return parent::_beforeSave();
    }
}
