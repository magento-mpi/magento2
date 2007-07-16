<?php
/**
 * Customer log model
 *
 * @package     Mage
 * @subpackage  Log
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Log_Model_Customer extends Mage_Core_Model_Abstract
{
    public function __construct()
    {
        $this->setResourceModel('log/customer');
    }

    public function getIdFieldName()
    {
        return 'id';
    }

    public function getLastActivity()
    {
        $this->getResource()->getLastActivity($this);
        return $this;
    }

    public function getLogTime()
    {
        $this->getResource()->getLogTime($this);
        return $this;
    }

    public function getOnlineStatus()
    {
        $this->getResource()->getOnlineStatus($this);
        return $this;
    }

    public function getLastQuote()
    {
        $this->getResource()->getLastQuote($this);
        return $this;
    }
}