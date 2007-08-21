<?php
/**
 * Manage currency block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Currency extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'system_currency';
        $this->_headerText = __('Manage Currencies');
        parent::__construct();
        $this->_removeButton('add');
        $this->_addButton('import',
            array(
                'label'     => __('Import Rates From www.webservicex.net'),
                'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/import') .'\')',
                'class'     => 'save',
            )
        );
    }
}
