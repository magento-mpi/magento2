<?php
/**
 * Customer account form block
 *
 * @package     Mage
 * @subpackage  Adminhtml
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Alexander Stadnitski <alexander@varien.com>
 */
class Mage_Adminhtml_Block_Cms_Page_Edit_Tab_Meta extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('page_');

        $page = Mage::registry('cms_page');

        $fieldset = $form->addFieldset('meta_fieldset', array('legend' => __('Meta Data')));

    	$fieldset->addField('meta_keywords', 'editor', array(
            'name' => 'meta_keywords',
            'label' => __('Keywords'),
            'title' => __('Meta Keywords'),
            'style' => 'width: 100%',
        ));

    	$fieldset->addField('meta_description', 'editor', array(
            'name' => 'meta_description',
            'label' => __('Description'),
            'title' => __('Meta Description'),
            'style' => 'width: 100%',
        ));

        $form->setValues($page->getData());

        $this->setForm($form);

        return $this;
    }

}
