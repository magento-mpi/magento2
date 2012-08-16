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
 * Staging backup edit tabs
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Adminhtml_Backup_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('enterprise_staging_backup_tabs');
        $this->setDestElementId('enterprise_staging_backup_form');
        $this->setTitle(Mage::helper('Enterprise_Staging_Helper_Data')->__('Websites Backup Information'));
    }

    /**
     * Preparing global layout
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Edit_Tabs
     */
    protected function _prepareLayout()
    {
        $this->addTab('backup_general_info', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Backup General Info'),
            'content'   => $this->getLayout()
                ->createBlock('Enterprise_Staging_Block_Adminhtml_Backup_Edit_Tabs_General')
                ->toHtml()
        ));

        $this->addTab('rollback', array(
            'label'     => Mage::helper('Enterprise_Staging_Helper_Data')->__('Rollback'),
            'content'   => $this->getLayout()
                ->createBlock('Enterprise_Staging_Block_Adminhtml_Backup_Edit_Tabs_Rollback')
                ->toHtml()
        ));

        return parent::_prepareLayout();
    }
}
