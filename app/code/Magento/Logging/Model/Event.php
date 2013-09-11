<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Logging event model
 */
namespace Magento\Logging\Model;

class Event extends \Magento\Core\Model\AbstractModel
{
    const RESULT_SUCCESS = 'success';
    const RESULT_FAILURE = 'failure';


    /**
     * Constructor
     */
    public function _construct()
    {
        $this->_init('\Magento\Logging\Model\Resource\Event');
    }

    /**
     * Set some data automatically before saving model
     *
     * @return \Magento\Logging\Model\Event
     */
    protected function _beforeSave()
    {
        if (!$this->getId()) {
            $this->setStatus($this->getIsSuccess() ? self::RESULT_SUCCESS : self::RESULT_FAILURE);
            if (!$this->getUser() && $id = $this->getUserId()) {
                $this->setUser(\Mage::getModel('\Magento\User\Model\User')->load($id)->getUserName());
            }
            if (!$this->hasTime()) {
                $this->setTime(time());
            }
        }
        /**
         * Prepare short details data
         */
        $info = array();
        $info['general'] = $this->getInfo();
        if ($this->getAdditionalInfo()) {
            $info['additional'] = $this->getAdditionalInfo();
        }
        $this->setInfo(serialize($info));
        return parent::_beforeSave();
    }

    /**
     * Define if current event has event changes
     *
     * @return bool
     */
    public function hasChanges()
    {
        if ($this->getId()) {
            return (bool)$this->getResource()->getEventChangeIds($this->getId());
        }
        return false;
    }
}
