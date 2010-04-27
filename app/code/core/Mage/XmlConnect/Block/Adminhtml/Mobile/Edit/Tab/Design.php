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
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Design extends Mage_Adminhtml_Block_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    public function __construct()
    {
        parent::__construct();
        $this->setShowGlobalIcon(true);
    }

    protected function _fontList()
    {
        return array(
            array(
                'value' => 'HiraKakuProN-W3',
                'label' => 'HiraKakuProN-W3',
            ),
            array(
                'value' => 'Courier',
                'label' => 'Courier',
            ),
            array(
                'value' => 'Courier-BoldOblique',
                'label' => 'Courier-BoldOblique',
            ),
            array(
                'value' => 'Courier-Oblique',
                'label' => 'Courier-Oblique',
            ),
            array(
                'value' => 'Courier-Bold',
                'label' => 'Courier-Bold',
            ),
            array(
                'value' => 'ArialMT',
                'label' => 'ArialMT',
            ),
            array(
                'value' => 'Arial-BoldMT',
                'label' => 'Arial-BoldMT',
            ),
            array(
                'value' => 'Arial-BoldItalicMT',
                'label' => 'Arial-BoldItalicMT',
            ),
            array(
                'value' => 'Arial-ItalicMT',
                'label' => 'Arial-ItalicMT',
            ),
            array(
                'value' => 'STHeitiTC-Light',
                'label' => 'STHeitiTC-Light',
            ),
            array(
                'value' => 'STHeitiTC-Medium',
                'label' => 'STHeitiTC-Medium',
            ),
            array(
                'value' => 'AppleGothic',
                'label' => 'AppleGothic',
            ),
            array(
                'value' => 'CourierNewPS-BoldMT',
                'label' => 'CourierNewPS-BoldMT',
            ),
            array(
                'value' => 'CourierNewPS-ItalicMT',
                'label' => 'CourierNewPS-ItalicMT',
            ),
            array(
                'value' => 'CourierNewPS-BoldItalicMT',
                'label' => 'CourierNewPS-BoldItalicMT',
            ),
            array(
                'value' => 'CourierNewPSMT',
                'label' => 'CourierNewPSMT',
            ),
            array(
                'value' => 'Zapfino',
                'label' => 'Zapfino',
            ),
            array(
                'value' => 'HiraKakuProN-W6',
                'label' => 'HiraKakuProN-W6',
            ),
            array(
                'value' => 'ArialUnicodeMS',
                'label' => 'ArialUnicodeMS',
            ),
            array(
                'value' => 'STHeitiSC-Medium',
                'label' => 'STHeitiSC-Medium',
            ),
            array(
                'value' => 'STHeitiSC-Light',
                'label' => 'STHeitiSC-Light',
            ),
            array(
                'value' => 'AmericanTypewriter',
                'label' => 'AmericanTypewriter',
            ),
            array(
                'value' => 'AmericanTypewriter-Bold',
                'label' => 'AmericanTypewriter-Bold',
            ),
            array(
                'value' => 'Helvetica-Oblique',
                'label' => 'Helvetica-Oblique',
            ),
            array(
                'value' => 'Helvetica-BoldOblique',
                'label' => 'Helvetica-BoldOblique',
            ),
            array(
                'value' => 'Helvetica',
                'label' => 'Helvetica',
            ),
            array(
                'value' => 'Helvetica-Bold',
                'label' => 'Helvetica-Bold',
            ),
            array(
                'value' => 'MarkerFelt-Thin',
                'label' => 'MarkerFelt-Thin',
            ),
            array(
                'value' => 'HelveticaNeue',
                'label' => 'HelveticaNeue',
            ),
            array(
                'value' => 'HelveticaNeue-Bold',
                'label' => 'HelveticaNeue-Bold',
            ),
            array(
                'value' => 'DBLCDTempBlack',
                'label' => 'DBLCDTempBlack',
            ),
            array(
                'value' => 'Verdana-Bold',
                'label' => 'Verdana-Bold',
            ),
            array(
                'value' => 'Verdana-BoldItalic',
                'label' => 'Verdana-BoldItalic',
            ),
            array(
                'value' => 'Verdana',
                'label' => 'Verdana',
            ),
            array(
                'value' => 'Verdana-Italic',
                'label' => 'Verdana-Italic',
            ),
            array(
                'value' => 'TimesNewRomanPSMT',
                'label' => 'TimesNewRomanPSMT',
            ),
            array(
                'value' => 'TimesNewRomanPS-BoldMT',
                'label' => 'TimesNewRomanPS-BoldMT',
            ),
            array(
                'value' => 'TimesNewRomanPS-BoldItalicMT',
                'label' => 'TimesNewRomanPS-BoldItalicMT',
            ),
            array(
                'value' => 'TimesNewRomanPS-ItalicMT',
                'label' => 'TimesNewRomanPS-ItalicMT',
            ),
            array(
                'value' => 'Georgia-Bold',
                'label' => 'Georgia-Bold',
            ),
            array(
                'value' => 'Georgia',
                'label' => 'Georgia',
            ),
            array(
                'value' => 'Georgia-BoldItalic',
                'label' => 'Georgia-BoldItalic',
            ),
            array(
                'value' => 'Georgia-Italic',
                'label' => 'Georgia-Italic',
            ),
            array(
                'value' => 'STHeitiJ-Medium',
                'label' => 'STHeitiJ-Medium',
            ),
            array(
                'value' => 'STHeitiJ-Light',
                'label' => 'STHeitiJ-Light',
            ),
            array(
                'value' => 'ArialRoundedMTBold',
                'label' => 'ArialRoundedMTBold',
            ),
            array(
                'value' => 'TrebuchetMS-Italic',
                'label' => 'TrebuchetMS-Italic',
            ),
            array(
                'value' => 'TrebuchetMS',
                'label' => 'TrebuchetMS',
            ),
            array(
                'value' => 'Trebuchet-BoldItalic',
                'label' => 'Trebuchet-BoldItalic',
            ),
            array(
                'value' => 'TrebuchetMS-Bold',
                'label' => 'TrebuchetMS-Bold',
            ),
            array(
                'value' => 'STHeitiK-Medium',
                'label' => 'STHeitiK-Medium',
            ),
            array(
                'value' => 'STHeitiK-Light',
                'label' => 'STHeitiK-Light',
            ),
        );
    }

    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('logo', array('legend' => $this->__('Logo')));

        $fieldset->addField('conf_logo_header_image', 'text', array(
            'name'      => 'conf_logo_header_image',
            'label'     => $this->__('Header Logo Image'),
        ));

        $fieldset->addField('conf_logo_header_position', 'select', array(
            'name'      => 'conf_logo_header_position',
            'label'     => $this->__('Header Logo Position'),
            'values'    => array(
                array(
                    'value' => 'above',
                    'label' => $this->__('Above screen title'),
                ),
            ),
        ));

        $fieldset->addField('conf_logo_body_image', 'text', array(
            'name'      => 'conf_logo_body_image',
            'label'     => $this->__('Body Logo Image'),
        ));

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('color', array('legend' => $this->__('Color Themes')));

        $fieldset->addField('conf_color_header_background', 'text', array(
            'name'      => 'conf_color_header_background',
            'label'     => $this->__('Header Background Color'),
        ));

        $fieldset->addField('conf_color_header', 'text', array(
            'name'      => 'conf_color_header',
            'label'     => $this->__('Header Text'),
        ));

        $fieldset->addField('conf_color_primary', 'text', array(
            'name'      => 'conf_color_primary',
            'label'     => $this->__('Primary'),
        ));

        $fieldset->addField('conf_color_secondary', 'text', array(
            'name'      => 'conf_color_secondary',
            'label'     => $this->__('Secondary'),
        ));

        $fieldset->addField('conf_color_body', 'text', array(
            'name'      => 'conf_color_body',
            'label'     => $this->__('Body Text'),
        ));

        $fieldset->addField('conf_color_price', 'text', array(
            'name'      => 'conf_color_price',
            'label'     => $this->__('Price Text'),
        ));

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('tabs', array('legend' => $this->__('Tabs')));

        //$fieldset->addField('conf_tab_home_label', 'text', array(
        //    'name'      => 'conf_tab_home_label',
        //    'label'     => $this->__('Home Tab Label'),
        //));

        $fieldset->addField('conf_tab_home_icon', 'text', array(
            'name'      => 'conf_tab_home_icon',
            'label'     => $this->__('Home Tab Icon'),
        ));

        //$fieldset->addField('conf_tab_shop_label', 'text', array(
        //    'name'      => 'conf_tab_shop_label',
        //    'label'     => $this->__('Shop Tab Label'),
        //));

        $fieldset->addField('conf_tab_shop_icon', 'text', array(
            'name'      => 'conf_tab_shop_icon',
            'label'     => $this->__('Shop Tab Icon'),
        ));

        //$fieldset->addField('conf_tab_search_label', 'text', array(
        //    'name'      => 'conf_tab_search_label',
        //    'label'     => $this->__('Search Tab Label'),
        //));

        $fieldset->addField('conf_tab_search_icon', 'text', array(
            'name'      => 'conf_tab_search_icon',
            'label'     => $this->__('Search Tab Icon'),
        ));

        //$fieldset->addField('conf_tab_cart_label', 'text', array(
        //    'name'      => 'conf_tab_cart_label',
        //    'label'     => $this->__('Cart Tab Label'),
        //));

        $fieldset->addField('conf_tab_cart_icon', 'text', array(
            'name'      => 'conf_tab_cart_icon',
            'label'     => $this->__('Cart Tab Icon'),
        ));

        //$fieldset->addField('conf_tab_more_label', 'text', array(
        //    'name'      => 'conf_tab_more_label',
        //    'label'     => $this->__('More Tab Label'),
        //));

        $fieldset->addField('conf_tab_more_icon', 'text', array(
            'name'      => 'conf_tab_more_icon',
            'label'     => $this->__('More Tab Icon'),
        ));

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('fonts', array('legend' => $this->__('Fonts')));

        $fieldset->addField('conf_font_header', 'select', array(
            'name'      => 'conf_font_header',
            'label'     => $this->__('Header Font'),
            'values'    => $this->_fontList(),
        ));

        $fieldset->addField('conf_font_header_size', 'text', array(
            'name'      => 'conf_font_header_size',
            'label'     => $this->__('Header Font Size'),
        ));

        $fieldset->addField('conf_font_tabbar', 'select', array(
            'name'      => 'conf_font_tabbar',
            'label'     => $this->__('Tabs Font'),
            'values'    => $this->_fontList(),
        ));

        $fieldset->addField('conf_font_tabbar_size', 'text', array(
            'name'      => 'conf_font_tabbar_size',
            'label'     => $this->__('Tabs Font Size'),
        ));

        $model = Mage::registry('current_app');
        $form->setValues($model->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Application Design');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Application Design');
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
     * @return true
     */
    public function isHidden()
    {
        return false;
    }
}
