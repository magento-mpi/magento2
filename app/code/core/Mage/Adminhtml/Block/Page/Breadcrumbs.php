<?php
/**
 * Adminhtml page breadcrumbs
 *
 * @package     Mage
 * @subpackage  Breadcrumbs
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_Page_Breadcrumbs extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        $this->setTemplate('adminhtml/page/breadcrumbs.phtml');
    }
}
