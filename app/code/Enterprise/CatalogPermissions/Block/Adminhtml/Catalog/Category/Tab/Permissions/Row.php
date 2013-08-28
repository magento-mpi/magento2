<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml permissions row block
 *
 * @category   Enterprise
 * @package    Enterprise_CatalogPermissions
 */
class Enterprise_CatalogPermissions_Block_Adminhtml_Catalog_Category_Tab_Permissions_Row
    extends Magento_Adminhtml_Block_Catalog_Category_Abstract
{

    protected $_template = 'catalog/category/tab/permissions/row.phtml';

    protected function _prepareLayout()
    {
        $this->addChild('delete_button', 'Magento_Adminhtml_Block_Widget_Button', array(
            //'label' => __('Remove Permission'),
            'class' => 'delete' . ($this->isReadonly() ? ' disabled' : ''),
            'disabled' => $this->isReadonly(),
            'type'  => 'button',
            'id'    => '{{html_id}}_delete_button'
        ));

        return parent::_prepareLayout();
    }

    /**
     * Check edit by websites
     *
     * @return boolean
     */
    public function canEditWebsites()
    {
        return !Mage::app()->hasSingleStore();
    }

    /**
     * Check is block readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getCategory()->getPermissionsReadonly();
    }

    public function getDefaultWebsiteId()
    {
        return Mage::app()->getStore(true)->getWebsiteId();
    }

    /**
     * Retrieve list of permission grants
     *
     * @return array
     */
    public function getGrants()
    {
        return array(
            'grant_catalog_category_view' => __('Browsing Category'),
            'grant_catalog_product_price' => __('Display Product Prices'),
            'grant_checkout_items' => __('Add to Cart')
        );
    }

    /**
     * Retrieve field class name
     *
     * @param string $fieldId
     * @return string
     */
    public function getFieldClassName($fieldId)
    {
        return strtr($fieldId, '_', '-') . '-value';
    }

    /**
     * Retrieve websites collection
     *
     * @return Magento_Core_Model_Resource_Website_Collection
     */
    public function getWebsiteCollection()
    {
        if (!$this->hasData('website_collection')) {
            $collection = Mage::getModel('Magento_Core_Model_Website')->getCollection();
            $this->setData('website_collection', $collection);
        }

        return $this->getData('website_collection');
    }

    /**
     * Retrieve customer group collection
     *
     * @return Magento_Customer_Model_Resource_Group_Collection
     */
    public function getCustomerGroupCollection()
    {
        if (!$this->hasData('customer_group_collection')) {
            $collection = Mage::getModel('Magento_Customer_Model_Group')->getCollection();
            $this->setData('customer_group_collection', $collection);
        }

        return $this->getData('customer_group_collection');
    }

    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('delete_button');
    }
}
