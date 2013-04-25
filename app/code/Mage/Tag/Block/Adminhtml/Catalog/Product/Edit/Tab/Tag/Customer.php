<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage_Tag
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * Customer tagged products tab
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method     Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer setTitle() setTitle(string $title)
 * @method     array getTitle() getTitle()
 */

class Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag_Customer
    extends Mage_Backend_Block_Template
    implements Mage_Backend_Block_Widget_Tab_Interface
{
    /**
     * Id of current tab
     */
    const TAB_ID = 'customers_tags';

    /**
     * Array of data helpers
     *
     * @var array
     */
    protected $_helpers;

    /**
     * Authentication session
     *
     * @var Mage_Core_Model_Authorization
     */
    protected $_authSession;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Authorization $authSession
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Authorization $authSession,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        if (isset($data['helpers'])) {
            $this->_helpers = $data['helpers'];
        }

        $this->_authSession = $authSession;
        $this->setId(self::TAB_ID);
        $this->setTitle($this->_helper('Mage_Tag_Helper_Data')->__('Customers Tagged Product'));
    }

    /**
     * Helper getter
     *
     * @param string $helperName
     * @return Mage_Core_Helper_Abstract
     */
    protected function _helper($helperName)
    {
        return isset($this->_helpers[$helperName]) ? $this->_helpers[$helperName] : Mage::helper($helperName);
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
        return $this->_authSession->isAllowed('Mage_Tag::tag_all');
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
        return 'reviews';
    }

    /**
     * @return string
     */
    public function getGroupCode()
    {
        return Mage_Adminhtml_Block_Catalog_Product_Edit_Tabs::ADVANCED_TAB_GROUP_CODE;
    }
}
