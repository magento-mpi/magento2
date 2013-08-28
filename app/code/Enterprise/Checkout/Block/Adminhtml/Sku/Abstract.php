<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin Checkout main form container
 *
 * @method string                                           getAdditionalJavascript()
 * @method string                                           getListType()
 * @method Enterprise_Checkout_Block_Adminhtml_Sku_Abstract setListType()
 * @method string                                           getDataContainerId()
 * @method Enterprise_Checkout_Block_Adminhtml_Sku_Abstract setDataContainerId()
 *
 * @category    Enterprise
 * @package     Enterprise_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Enterprise_Checkout_Block_Adminhtml_Sku_Abstract extends Magento_Adminhtml_Block_Template
{
    /**
     * List type of current block
     */
    const LIST_TYPE = 'add_by_sku';

    protected $_template = 'sku/add.phtml';

    /**
     * Initialize SKU container
     */
    protected function _construct()
    {

        // Used by JS to tell accordions from each other
        $this->setId('sku');
        /* @see Enterprise_Checkout_Controller_Adminhtml_Checkout::_getListItemInfo() */
        $this->setListType(self::LIST_TYPE);
        $this->setDataContainerId('sku_container');
    }

    /**
     * Define ADD and DEL buttons
     *
     * @return Enterprise_Checkout_Block_Adminhtml_Sku_Abstract
     */
    protected function _prepareLayout()
    {
        /* @var $headBlock Magento_Page_Block_Html_Head */
        $headBlock = parent::_prepareLayout()->getLayout()->getBlock('head');
        if ($headBlock) {
            // Head block is not defined on AJAX request
            $headBlock->addJs('Enterprise_Checkout::addbysku.js');
        }

        $this->addChild('deleteButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'   => '',
            'onclick' => 'addBySku.del(this)',
            'class'   => 'delete'
        ));

        $this->addChild('addButton', 'Magento_Adminhtml_Block_Widget_Button', array(
            'label'   => 'Add',
            'onclick' => 'addBySku.add()',
            'class'   => 'add'
        ));

        return $this;
    }

    /**
     * HTML of "+" button, which adds new field for SKU and qty
     *
     * @return string
     */
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('addButton');
    }

    /**
     * HTML of "x" button, which removes field with SKU and qty
     *
     * @return string
     */
    public function getDeleteButtonHtml()
    {
        return $this->getChildHtml('deleteButton');
    }

    /**
     * Returns URL to which CSV file should be submitted
     *
     * @abstract
     * @return string
     */
    abstract public function getFileUploadUrl();

    /**
     * Configuration data for AddBySku instance
     *
     * @return string
     */
    public function getAddBySkuDataJson()
    {
        $data = array(
            'dataContainerId'  => $this->getDataContainerId(),
            'deleteButtonHtml' => $this->getDeleteButtonHtml(),
            'fileUploaded'     => Enterprise_Checkout_Helper_Data::REQUEST_PARAMETER_SKU_FILE_IMPORTED_FLAG,
            // All functions requiring listType affects error grid only
            'listType'         => Enterprise_Checkout_Block_Adminhtml_Sku_Errors_Abstract::LIST_TYPE,
            'errorGridId'      => $this->getErrorGridId(),
            'fileFieldName'    => Enterprise_Checkout_Model_Import::FIELD_NAME_SOURCE_FILE,
            'fileUploadUrl'    => $this->getFileUploadUrl(),
        );

        $json = Mage::helper('Magento_Core_Helper_Data')->jsonEncode($data);
        return $json;
    }

    /**
     * JavaScript instance of AdminOrder or AdminCheckout
     *
     * @abstract
     * @return string
     */
    abstract public function getJsOrderObject();

    /**
     * HTML ID of error grid container
     *
     * @abstract
     * @return string
     */
    abstract public function getErrorGridId();

    /**
     * Retrieve context specific JavaScript
     *
     * @return string
     */
    public function getContextSpecificJs()
    {
        return '';
    }

    /**
     * Retrieve additional JavaScript
     *
     * @return string
     */
    public function getAdditionalJavascript()
    {
        return '';
    }
}
