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
