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

/**
 * Tab for Social Networking settings
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Social
    extends Mage_XmlConnect_Block_Adminhtml_Mobile_Widget_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    /**
     * Prepare form before rendering HTML
     * Setting Form Fieldsets and fields
     *
     * @return Mage_Adminhtml_Block_Widget_Form
     */
    protected function _prepareForm()
    {
        $form = new Varien_Data_Form();

        $this->setForm($form);

        $data = Mage::helper('xmlconnect')->getApplication()->getFormData();

        $yesNoValues = Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray();

        $fieldset = $form->addFieldset('social_networking', array(
            'legend'    => $this->__('Social Networking'),
        ));

        /**
         * Default values for social networks is DISABLED
         */
        $twitterStatus = $facebookStatus = $linkedinStatus = 0;

        if (isset($data['conf[native][socialNetworking][twitterEnabled]'])) {
            $twitterStatus = $data['conf[native][socialNetworking][twitterEnabled]'];
        }

        $fieldset->addField('conf/native/socialNetworking/twitterEnabled', 'select', array(
            'label'     => $this->__('Enable Twitter'),
            'name'      => 'conf[native][socialNetworking][twitterEnabled]',
            'values'    => $yesNoValues,
            'value'     => $twitterStatus,
        ));

        if (isset($data['conf[native][socialNetworking][facebookEnabled]'])) {
            $facebookStatus = $data['conf[native][socialNetworking][facebookEnabled]'];
        }

        $fieldset->addField('conf/native/socialNetworking/facebookEnabled', 'select', array(
            'label'     => $this->__('Enable Facebook'),
            'name'      => 'conf[native][socialNetworking][facebookEnabled]',
            'values'    => $yesNoValues,
            'value'     => $facebookStatus,
        ));

        if (isset($data['conf[native][socialNetworking][linkedinEnabled]'])) {
            $linkedinStatus = $data['conf[native][socialNetworking][linkedinEnabled]'];
        }

        $fieldset->addField('conf/native/socialNetworking/linkedinEnabled', 'select', array(
            'label'     => $this->__('Enable LinkedIn'),
            'name'      => 'conf[native][socialNetworking][linkedinEnabled]',
            'values'    => $yesNoValues,
            'value'     => $linkedinStatus,
        ));

        return parent::_prepareForm();
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Social Networking');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Social Networking');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return (bool) !Mage::getSingleton('adminhtml/session')->getNewApplication();
    }

    /**
     * Check if tab hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }
}
