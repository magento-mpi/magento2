<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Staging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Staging edit tabs
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Staging_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('enterprise_staging_tabs');
        $this->setDestElementId('enterprise_staging_form');
        $this->setTitle(Mage::helper('Enterprise_Staging_Helper_Data')->__('Staging Website Information'));
    }

    /**
     * Preparing global layout
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $type = $this->getStaging()->getType();
        if (!$type) {
            // try to find staging type in request parameters
            $type = $this->getRequest()->getParam('type', null);
        }

        if ($type) {
            $this->addTab('website', array(
                'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('General Information'),
                'content'   => $this->getLayout()
                    ->createBlock('Enterprise_Staging_Block_Adminhtml_Staging_Edit_Tabs_Website')
                    ->toHtml(),
            ));
        } else {
            $this->addTab('set', array(
                'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Settings'),
                'content'   => $this->getLayout()
                    ->createBlock('Enterprise_Staging_Block_Adminhtml_Staging_Edit_Tabs_Settings')
                    ->toHtml(),
                'active'    => true
            ));
        }
        return parent::_prepareLayout();
    }

    /**
     * Retrive current staging
     *
     * @return Enterprise_Staging_Model_Staging
     */
    public function getStaging()
    {
        if (!($this->getData('staging') instanceof Enterprise_Staging_Model_Staging)) {
            $this->setData('staging', Mage::registry('staging'));
        }
        return $this->getData('staging');
    }
}
