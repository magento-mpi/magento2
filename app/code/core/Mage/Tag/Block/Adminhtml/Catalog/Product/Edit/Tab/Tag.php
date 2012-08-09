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
 * Products tags tab
 *
 * @category   Mage
 * @package    Mage_Tag
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @method     Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag setTitle() setTitle(string $title)
 * @method     array getTitle() getTitle()
 */

class Mage_Tag_Block_Adminhtml_Catalog_Product_Edit_Tab_Tag
    extends Mage_Backend_Block_Template
    implements Mage_Backend_Block_Widget_Tab_Interface
{
    /**
     * Array of data helpers
     *
     * @var array
     */
    protected $_helpers;

    /**
     * Authentication session
     *
     * @var Mage_Backend_Model_Auth_Session
     */
    protected $_authSession;

    /**
     * Set identifier and title
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        parent::__construct($data);

        if (isset($data['helpers'])) {
            $this->_helpers = $data['helpers'];
        }

        if (isset($data['auth_session'])) {
            $this->_authSession = $data['auth_session'];
        } else {
            $this->_authSession = Mage::getSingleton('Mage_Backend_Model_Auth_Session');
        }

        $this->setId('tags');
        $this->setTitle($this->_helper('Mage_Tag_Helper_Data')->__('Product Tags'));
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
        return $this->_authSession->isAllowed('Mage_Tag::tag');
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
        return $this->getUrl('*/*/tagGrid', array('_current' => true));
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
}
