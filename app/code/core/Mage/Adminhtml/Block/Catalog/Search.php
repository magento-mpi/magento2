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

class Mage_Adminhtml_Block_Catalog_Search extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'catalog_search';
        $this->_headerText = __('Catalog Searches');
        $this->_addButtonLabel = __('Add New Search');
        parent::__construct();
        
    }
}