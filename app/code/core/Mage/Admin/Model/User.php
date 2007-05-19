<?php
/**
 * ACL user model
 *
 * @package     Mage
 * @subpackage  Admin
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Admin_Model_User extends Varien_Object
{
    public function __construct() 
    {
        parent::__construct();
    }
    
    public function getId()
    {
        return $this->getUserId();
    }
    
    /**
     * Get resource model
     *
     * @return mixed
     */
    public function getResource()
    {
        return Mage::getSingleton('admin_resource', 'user');
    }
    
    public function load($userId)
    {
        $this->setData($this->getResource()->load($customerId));
        return $this;
    }
}