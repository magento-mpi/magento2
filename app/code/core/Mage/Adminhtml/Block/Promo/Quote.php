<?php
/**
 * Catalog price rules
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  Promo
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */

class Mage_Adminhtml_Block_Promo_Quote extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'promo_quote';
        $this->_headerText = __('Shopping Cart Price Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::__construct();
        
    }
}