<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml tier price item renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
    extends Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Group_Abstract
{

    protected $_template = 'catalog/product/edit/price/tier.phtml';

    /**
     * Retrieve list of initial customer groups
     *
     * @return array
     */
    protected function _getInitialCustomerGroups()
    {
        return array(Magento_Customer_Model_Group::CUST_GROUP_ALL => __('ALL GROUPS'));
    }

    /**
     * Sort values
     *
     * @param array $data
     * @return array
     */
    protected function _sortValues($data)
    {
        usort($data, array($this, '_sortTierPrices'));
        return $data;
    }

    /**
     * Sort tier price values callback method
     *
     * @param array $a
     * @param array $b
     * @return int
     */
    protected function _sortTierPrices($a, $b)
    {
        if ($a['website_id'] != $b['website_id']) {
            return $a['website_id'] < $b['website_id'] ? -1 : 1;
        }
        if ($a['cust_group'] != $b['cust_group']) {
            return $this->getCustomerGroups($a['cust_group']) < $this->getCustomerGroups($b['cust_group']) ? -1 : 1;
        }
        if ($a['price_qty'] != $b['price_qty']) {
            return $a['price_qty'] < $b['price_qty'] ? -1 : 1;
        }

        return 0;
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return Magento_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Widget_Button')
            ->setData(array(
                'label' => __('Add Tier'),
                'onclick' => 'return tierPriceControl.addItem()',
                'class' => 'add'
            ));
        $button->setName('add_tier_price_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }

}
