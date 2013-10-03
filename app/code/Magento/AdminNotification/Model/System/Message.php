<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\AdminNotification\Model\System;

class Message extends \Magento\Core\Model\AbstractModel
    implements \Magento\AdminNotification\Model\System\MessageInterface
{
    protected function _construct()
    {
        $this->_init('Magento\AdminNotification\Model\Resource\System\Message');
    }

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return true;
    }

    /**
     * Retrieve message text
     *
     * @return text
     */
    public function getText()
    {
        return $this->getData('text');
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return $this->_getData('severity');
    }

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity()
    {
        return $this->_getData('identity');
    }
}
