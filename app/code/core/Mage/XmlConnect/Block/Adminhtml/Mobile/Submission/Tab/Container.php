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
 * Device container block
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Block_Adminhtml_Mobile_Submission_Tab_Container
    extends Mage_Adminhtml_Block_Template
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    protected $_template = 'submission/container.phtml';

    /**
     * Constructor
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setShowGlobalIcon(true);

    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return $this->__('Submission');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return $this->__('Submission');
    }

    /**
     * Returns status flag about this tab can be shown or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    /**
     * Retrive submission action url
     *
     * @return string
     */
    public function getActionUrl()
    {
        $param = array('key' => Mage::helper('Mage_XmlConnect_Helper_Data')->getApplication()->getId());
        return $this->getUrl('*/*/submissionPost', $param);
    }
}
