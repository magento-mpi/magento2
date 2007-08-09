<?php
/**
 * description
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @subpackage  Promo_Quote
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

    public function __construct()
    {
        parent::__construct();
        $this->setId('promo_catalog_edit_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Checkout Price Rule'));
    }

    protected function _beforeToHtml()
    {
        $this->addTab('main_section', array(
            'label'     => __('Rule Information'),
            'title'     => __('Rule Information'),
            'content'   => $this->getLayout()->createBlock('adminhtml/promo_quote_edit_tab_main')->toHtml(),
            'active'    => true
        ));

        $this->addTab('condact_section', array(
            'label'     => __('Conditions and Actions'),
            'title'     => __('Conditions and Actions'),
            'content'   => $this->getLayout()->createBlock('adminhtml/promo_quote_edit_tab_condact')->toHtml(),
        ));
        /*
        $this->addTab('actions_section', array(
            'label'     => __('Actions'),
            'title'     => __('Actions'),
            'content'   => $this->getLayout()->createBlock('adminhtml/promo_catalog_edit_tab_actions')->toHtml(),
        ));
        */
        return parent::_beforeToHtml();
    }

}
