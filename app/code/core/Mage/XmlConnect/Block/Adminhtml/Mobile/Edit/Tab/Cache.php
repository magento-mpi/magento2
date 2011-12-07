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
 * Tab for Cache Management
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Edit_Tab_Cache
    extends Mage_Adminhtml_Block_Widget_Form
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

        $data = Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication()->getFormData();

        $fieldset = $form->addFieldset('cache_management', array('legend' => $this->__('Cache Management')));
        if (isset($data['conf[native][cacheLifetime]'])) {
            $lifetime = $data['conf[native][cacheLifetime]'];
        } else {
            $lifetime = Mage::helper('Mage_XmlConnect_Helper_Data')->getDefaultCacheLifetime();
        }
        $fieldset->addField('conf/native/cacheLifetime', 'text', array(
            'label'     => $this->__('Cache Lifetime (seconds)'),
            'name'      => 'conf[native][cacheLifetime]',
            'value'     => $lifetime,
            'note'      => $this->__('If you want to disable the cache on the application side, leave the field empty. Warning! When disabling cache, the application will take time to load each page.'),
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
        return $this->__('Cache Management');
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Cache Management');
    }

    /**
     * Check if tab can be shown
     *
     * @return bool
     */
    public function canShowTab()
    {
        return (bool) !Mage::getSingleton('Mage_Adminhtml_Model_Session')->getNewApplication();
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
