<?php
/**
 * Adminhtml customers page content block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 */
class Mage_Adminhtml_Block_Catalog_Producttags extends Mage_Core_Block_Template 
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('adminhtml/catalog/tags/index.phtml');
    }
}
