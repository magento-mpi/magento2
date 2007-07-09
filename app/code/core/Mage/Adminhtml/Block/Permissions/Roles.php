<?php
/**
 * user roles block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Permissions_Roles extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/permissions/roles.phtml');
    }
    
    public function getAddNewUrl()
    {
        return $this->getUrl('*/*/editroles');
    }
    
    public function getGridHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/permissions_grid_role')->toHtml();
    }
}
