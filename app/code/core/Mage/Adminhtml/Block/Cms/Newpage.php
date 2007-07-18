<?php
/**
 * Adminhtml cms new page block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms_Newpage extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('adminhtml/cms/addeditForm.phtml');
        $this->setDestElementId('page_form');
    }

    public function getSaveUrl()
    {
        return Mage::getUrl('adminhtml/cms/savepage');
    }

    protected function _beforeToHtml()
    {
        $form = new Varien_Data_Form();
        $fieldset = $form->addFieldset('address_fieldset', array('legend'=>__('Edit Customer Address')));

      	$fieldset->addField('123', 'text',
                array(
                    'name'  => '',
                    'label' => '',
                    'title' => '',
                    'class' => '',
                )
            );

        $this->setForm($form);

        $this->assign('header', __('Add New Page'));
        return parent::_beforeToHtml();
    }
}
