<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Content
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_pages;

    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    /**
     * Add page input to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldPrefix
     * @param string $title
     * @param bool $simple
     */
    protected function addPage($fieldset, $fieldPrefix, $title=NULL, $simple=FALSE)
    {
        $title = $this->getDefaultTitle($title, $fieldPrefix);
        $el = $fieldset->addField($fieldPrefix, 'page', array(
            'name'      => $fieldPrefix,
        ));
        $el->initFields(array(
            'name'      => $fieldPrefix,
            'values'    => $this->_pages,
        ));
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();
        $this->setForm($form);

        $pages = Mage::getModel('cms/page')->getResourceCollection()->toOptionIdArray();
        $dummy = array(array( 'value' => '', 'label' => '' ));
        $this->_pages = array_merge($dummy, $pages);

        $fieldset = $form->addFieldset('cmsPages', array('legend' => $this->__('Pages')));
        $this->_addElementTypes($fieldset);
        $this->addPage($fieldset, 'conf[pages][0]');
        $this->addPage($fieldset, 'conf[pages][1]');
        $this->addPage($fieldset, 'conf[pages][2]');
        $this->addPage($fieldset, 'conf[pages][3]');
        $this->addPage($fieldset, 'conf[pages][4]');
        // FIXME

        $model = Mage::registry('current_app');
        $form->setValues($model->getFormData());
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Content');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Content');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return false
     */
    public function isHidden()
    {
        return false;
    }
}
