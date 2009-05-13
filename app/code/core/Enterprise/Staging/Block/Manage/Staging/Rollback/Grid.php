<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Staging Rollback Grid
 *
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging_Rollback_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();

        $this->setPagerVisibility(false);
        $this->setFilterVisibility(false);

        $this->setTemplate('enterprise/staging/manage/staging/rollback/grid.phtml');
    }

    /**
     * PrepareCollection method.
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Rollback_Grid
     */
    protected function _prepareCollection()
    {
        $collection = new Varien_Data_Collection();

        foreach (Mage::getSingleton('enterprise_staging/staging_config')->getStagingItems() as $stagingItem) {
            if ((int)$stagingItem->is_backend) {
                continue;
            }

            $this->_addStagingItemToCollection($collection, $stagingItem);

            if ($stagingItem->extends) {
                foreach ($stagingItem->extends->children() as $extendItem) {
                    if (!Mage::getSingleton('enterprise_staging/staging_config')->isItemModuleActive($extendItem)) {
                         continue;
                    }
                    $this->_addStagingItemToCollection($collection, $extendItem);
                }
            }
        }

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * Add items into collection object
     *
     * @param Varien_Data_Collection $collection
     * @param Varien_Simplexml_Element $stagingItem
     * @return Enterprise_Staging_Block_Manage_Staging_Rollback_Grid
     */
    protected function _addStagingItemToCollection($collection, $stagingItem)
    {
        $extendInfo = $this->getExtendInfo();

        $_code = (string) $stagingItem->getName();

        $item = Mage::getModel('enterprise_staging/staging_item')
            ->loadFromXmlStagingItem($stagingItem);

        $disabled = "none";
        $checked = true;
        $availabilityText = "";
        //process extend information
        if (!empty($extendInfo) && is_array($extendInfo) && isset($extendInfo[$_code])) {
            $item->addData($extendInfo[$_code]);
            if ($extendInfo[$_code]["disabled"]==true) {
                $disabled = "disabled";
                $checked = false;
                $availabilityText = '<div style="color:#800;">'.$extendInfo[$_code]["reason"].'</div>';
            } else {
                $availabilityText = '<div style="color:#080;"><b>'.Mage::helper('enterprise_staging')->__('available').'</b></div>';
            }
        }
        $item->setData("id", $_code);
        $item->setData("itemCheckbox", $this->_addFieldset($_code, $disabled, $checked));
        $item->setData("rollbackAvailability", $availabilityText);

        $collection->addItem($item);

        return $this;
    }

    /**
     * Return input checkbox
     *
     * @param string  $disabled
     * @param boolean $checked
     * @return string
     */
    protected function _addFieldset($code, $disabled, $checked)
    {
        $form = new Varien_Data_Form();

        $form->addField("checkbox" .$code , "checkbox" ,
            array(
                'value'   => $code,
                'name'    => "map[staging_items][{$code}][staging_item_code]",
                $disabled => true,
                'checked' => $checked
            )
        );

        return $form->toHtml();
    }

    /**
     * Configuration of grid columns
     *
     * @return Enterprise_Staging_Block_Manage_Staging_Rollback_Grid
     */
    protected function _prepareColumns()
    {
        $this->addColumn('itemCheckbox', array(
            'header'    => '',
            'index'     => 'itemCheckbox',
            'type'      => 'text',
            'truncate'  => 1000,
            'width'     => '20px'
        ));

        $this->addColumn('name', array(
            'header'    => 'Item Name',
            'index'     => 'name',
            'type'      => 'text',
        ));

        $this->addColumn('rollbackAvailability', array(
            'header'    => 'Rollback availability',
            'index'     => 'rollbackAvailability',
            'type'      => 'text',
        ));

        return $this;
    }
}
