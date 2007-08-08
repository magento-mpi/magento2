<?php
/**
 * Poll manager grid
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */

class Mage_Adminhtml_Block_Promo_Catalog extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'promo_catalog';
        $this->_headerText = __('Catalog Price Rules');
        $this->_addButtonLabel = __('Add New Rule');
        parent::__construct();
    }
}