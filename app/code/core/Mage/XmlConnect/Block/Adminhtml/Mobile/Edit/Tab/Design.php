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

        $fieldset = $form->addFieldset('images', array('legend' => $this->__('Image files')));

        $fieldset->addField('conf_logo_company_small', 'text', array(
            'name'      => 'conf_logo_company_small',
            'label'     => $this->__('Logo above the header'),
            'title'     => $this->__('Small logo appear above header text'),
        ));

        $fieldset->addField('conf_logo_company_left', 'text', array(
            'name'      => 'conf_logo_company_left',
            'label'     => $this->__('Logo on the left'),
            'title'     => $this->__('Logo appear to the left of header text'),
        ));

        $fieldset->addField('conf_logo_aboutus', 'text', array(
            'name'      => 'conf_logo_aboutus',
            'label'     => $this->__('Logo for ‘about us’ page'),
        ));

        $fieldset->addField('conf_background_subcategory', 'text', array(
            'name'      => 'conf_background_subcategory',
            'label'     => $this->__('Subcategory background'),
            'title'     => $this->__('Background image for subcategory pages'),
        ));

        $fieldset->addField('conf_background_header', 'text', array(
            'name'      => 'conf_background_header',
            'label'     => $this->__('Header background'),
            'title'     => $this->__('Header background image'),
        ));

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('sounds', array('legend' => $this->__('Sound files')));

        $fieldset->addField('conf_sound_startup', 'text', array(
            'name'      => 'conf_sound_startup',
            'label'     => $this->__('Startup sound'),
            'title'     => $this->__('Sound file that chimes upon app startups'),
        ));

        /**
         * Fieldset
         */

        $fieldset = $form->addFieldset('colors', array('legend' => $this->__('Colors')));

        $fieldset->addField('conf_color_header_bg', 'text', array(
            'name'      => 'conf_color_header_bg',
            'label'     => $this->__('Header background color')
        ));

        $fieldset->addField('conf_color_header', 'text', array(
            'name'      => 'conf_color_header',
            'label'     => $this->__('Header text color')
        ));

        $fieldset->addField('conf_color_primary', 'text', array(
            'name'      => 'conf_color_primary',
            'label'     => $this->__('Primary color')
        ));

        $fieldset->addField('conf_color_secondary', 'text', array(
            'name'      => 'conf_color_secondary',
            'label'     => $this->__('Secondary color')
        ));

        $fieldset->addField('conf_color_text', 'text', array(
            'name'      => 'conf_color_text',
            'label'     => $this->__('Text color')
        ));

        $fieldset->addField('conf_color_price', 'text', array(
            'name'      => 'conf_color_price',
            'label'     => $this->__('Price color')
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
