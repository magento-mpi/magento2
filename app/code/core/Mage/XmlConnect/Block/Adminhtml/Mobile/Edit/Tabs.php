<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Application Tabs block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tabs
    extends Mage_Adminhtml_Block_Widget_Tabs
{
    /**
     * Constructor
     * Setting grid_id, DOM destination element id, Title
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('mobile_app_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle($this->__('Manage Mobile App'));
    }

    /**
     * Preparing global layout
     *
     * @return Mage_Core_Block_Abstract
     */
    protected function _prepareLayout()
    {
        if (Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication()) {
            $this->addTab('set', array(
                'label'     => $this->__('Settings'),
                'content'   => $this->getLayout()
                    ->createBlock('Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Settings')
                    ->toHtml(),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }
}
