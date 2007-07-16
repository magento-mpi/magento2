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
        parent::__construct();
        $this->setResourceModel('log/customer');
    }
    
    public function load($customerId, $field=null)
    {
        $this->getResource()->load($this, $customerId);
        return $this;
    }
}