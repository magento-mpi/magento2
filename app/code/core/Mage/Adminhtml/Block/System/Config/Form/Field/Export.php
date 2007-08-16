<?php
/**
 * Export CSV button for shipping table rates
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Sergiy Lysak <sergey@varien.com>
 */
class Mage_Adminhtml_Block_System_Config_Form_Field_Export extends Varien_Data_Form_Element_Abstract
{
    public function getElementHtml()
    {
        $data = array(
            'label'     => 'Export CSV',
            'onclick'   => 'location.href=\''.Mage::getUrl("*/*/exportTablerates").'\'',
            'class'     => '',
        );
        
        $html = $this->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button')->setData($data)->toHtml();

        return $html;
    }
}
