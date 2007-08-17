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
        $buttonBlock = $this->getForm()->getParent()->getLayout()->createBlock('adminhtml/widget_button');

        $params = array(
            'website' => $buttonBlock->getRequest()->getParam('website')
        );
        
        $data = array(
            'label'     => 'Export CSV',
            'onclick'   => 'setLocation(\''.Mage::getUrl("*/*/exportTablerates", $params).'\')',
            'class'     => '',
        );
        
        $html = $buttonBlock->setData($data)->toHtml();

        return $html;
    }
}
