<?php
/**
 * Admin page grid actions
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms_Page_Actions extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _initForm()
    {
        $actionsUrl = Mage::getUrl('adminhtml/cms_page');
        $row = $this->getPageObject();

        $form = new Varien_Data_Form();
    	$form->addField('action_'.$row->getPageId(), 'select',
            array(
                'name' => 'page_title',
                'html_id' => 'action_'.$row->getPageId(),
                'values' => array(
                    array(
                        'value' => $actionsUrl . 'edit/page/' . $row->getPageId(),
                        'label' => __('edit page'),
                    ),

                    array(
                        'value' => $actionsUrl . 'delete/page/' . $row->getPageId(),
                        'label' => __('delete page'),
                    ),

                    array(
                        'value' => $actionsUrl . (( $row->getPageActive() == 0 ) ? 'enable/page/' : 'disable/page/') . $row->getPageId(),
                        'label' => __( (($row->getPageActive() == 0 ) ? 'enable' : 'disable') . ' page' ),
                    )
                )
            )
        );

        $form->addField(null, 'button',
            array(
                'value' => __(' go '),
                'on_click' => "window.location.href=document.getElementById('action_{$row->getPageId()}').value"
            )
        );

        $this->setForm($form);
    }

    protected function _beforeToHtml()
    {
        $this->_initForm();
        return parent::_beforeToHtml();
    }
}
