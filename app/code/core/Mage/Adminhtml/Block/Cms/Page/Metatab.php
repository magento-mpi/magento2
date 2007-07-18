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
class Mage_Adminhtml_Block_Cms_Page_Metatab extends Mage_Adminhtml_Block_Widget_Form
{
    public function __construct()
    {
        parent::__construct();
    }

    protected function _initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_main');

        $fieldset = $form->addFieldset('meta_fieldset', array('legend'=>__('Meta Data')));

    	$fieldset->addField('page_title', 'editor',
            array(
                'name' => 'page_meta_keywords',
                'label' => __('Keywords'),
                'title' => __('Meta Keywords'),
                'value' => $this->getPageObject()->getPageMetaKeywords()
            )
        );

    	$fieldset->addField('page_meta_description', 'editor',
            array(
                'name' => 'page_meta_description',
                'label' => __('Description'),
                'title' => __('Meta Description'),
                'value' => $this->getPageObject()->getPageMetaDescription()
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
