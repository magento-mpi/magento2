<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_Tag
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Customer tagged products tab
 *
 * @category   Magento
 * @package    Magento_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method     Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer setTitle() setTitle(string $title)
 * @method     array getTitle() getTitle()
 */

class Magento_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer
    extends Magento_Backend_Block_Template
    implements Magento_Backend_Block_Widget_Tab_Interface
{
    /**
     * Id of current tab
     */
    const TAB_ID = 'customers_tags';

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->setId(self::TAB_ID);
        $this->setTitle(__('Customers Tagged Product'));
    }

    /**
     * Tab label getter
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->getTitle();
    }

    /**
     * Tab title getter
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->getTitle();
    }

    /**
     * Check whether tab can be showed
     *
     * @return bool
     */
    public function canShowTab()
    {
        return $this->_authorization->isAllowed('Magento_Tag::tag_all');
    }

    /**
     * Check whether tab should be hidden
     *
     * @return bool
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass()
    {
        return 'ajax';
    }

    /**
     * Tab URL getter
     *
     * @return string
     */
    public function getTabUrl()
    {
        return $this->getUrl('*/*/tagCustomerGrid', array('_current' => true));
    }

    /**
     * Retrieve id of tab after which current tab will be rendered
     *
     * @return string
     */
    public function getAfter()
    {
        return 'product-reviews';
    }

    /**
     * @return string
     */
    public function getGroupCode()
    {
        return Magento_Adminhtml_Block_Catalog_Product_Edit_Tabs::ADVANCED_TAB_GROUP_CODE;
    }
}
