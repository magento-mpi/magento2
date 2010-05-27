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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
abstract class Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * If title is empty, construct it from field name ('backgroundImage' => 'Background Image')
     *
     * @param string $title
     * @param string $field
     * @return string
     */
    protected function getDefaultTitle($title, $field)
    {
        if (!is_null($title)) {
            return $title;
        }
        $field = preg_replace('/^.+?\[([^\]]+)\]$/', '$1', $field);
        $field = preg_replace('/([a-z])([A-Z])/', '$1 $2', $field);
        return ucwords($field);
    }

    /**
     * Add color chooser to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addColor($fieldset, $fieldName, $title=NULL)
    {
        $title = $this->getDefaultTitle($title, $fieldName);
        $fieldset->addField($fieldName, 'color', array(
            'name'      => $fieldName,
            'label'     => $this->__($title),
        ));
    }

    /**
     * Add image uploader to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldName
     * @param string $title
     */
    protected function addImage($fieldset, $fieldName, $title=NULL)
    {
        $title = $this->getDefaultTitle($title, $fieldName);
        $fieldset->addField($fieldName, 'image', array(
            'name'      => $fieldName,
            'label'     => $this->__($title),
        ));
    }

    /**
     * Add font selector to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldPrefix
     * @param string $title
     * @param bool $simple
     */
    protected function addFont($fieldset, $fieldPrefix, $title=NULL, $simple=FALSE)
    {
        $title = $this->getDefaultTitle($title, $fieldPrefix);
        $el = $fieldset->addField($fieldPrefix, 'font', array(
            'name'      => $fieldPrefix,
            'label'     => $this->__($title),
        ));
        $el->initFields(array(
            'name'      => $fieldPrefix,
            'fontNames' => Mage::helper('xmlconnect/iphone')->getFontList(),
            'fontSizes' => Mage::helper('xmlconnect/iphone')->getFontSizes(),
            'is_simple' => $simple,
        ));
    }

    /**
     * Add font selector (without color) to fieldset
     *
     * @param Varien_Data_Form_Element_Fieldset $fieldset
     * @param string $fieldPrefix
     * @param string $title
     */
    protected function addFontSimple($fieldset, $fieldPrefix, $title=NULL)
    {
        $this->addFont($fieldset, $fieldPrefix, $title, TRUE);
    }

    /**
     * Configure image element type
     *
     */
    protected function _getAdditionalElementTypes()
    {
        return array(
            'image' => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_image'),
            'font'  => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_font'),
            'color' => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_color'),
            'tabs'  => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_tabs'),
            'theme' => Mage::getConfig()->getBlockClassName('xmlconnect/adminhtml_mobile_helper_theme'),
        );
    }
}