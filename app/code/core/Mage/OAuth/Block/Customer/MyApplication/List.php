<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_Review
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Customer My Applications list block
 *
 * @category   Mage
 * @package    Mage_OAuth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Block_Customer_MyApplication_List extends Mage_Customer_Block_Account_Dashboard
{
    /**
     * Collection model
     *
     * @var Mage_OAuth_Model_Resource_Token_Collection
     */
    protected $_collection;

    /**
     * Revoke labels
     *
     * @var array
     */
    protected $_revokeLabels;

    /**
     * Prepare collection
     */
    protected function _construct()
    {
        /** @var $session Mage_Customer_Model_Session */
        $session = Mage::getSingleton('customer/session');

        /** @var $collection Mage_OAuth_Model_Resource_Token_Collection */
        $collection = Mage::getModel('oauth/token')->getCollection();
        $collection->joinConsumerAsApplication()
                ->addFilterByCustomerId($session->getCustomerId());
        $this->_collection = $collection;

        $this->_revokeLabels = array(
            $this->__('Enabled'),
            $this->__('Revoked'));
    }

    /**
     * Get count of total records
     *
     * @return int
     */
    public function count()
    {
        return $this->_collection->getSize();
    }

    /**
     * Get toolbar html
     *
     * @return string
     */
    public function getToolbarHtml()
    {
        return $this->getChildHtml('toolbar');
    }

    /**
     * Prepare layout
     *
     * @return Mage_OAuth_Block_Customer_MyApplication_List
     */
    protected function _prepareLayout()
    {
        /** @var $toolbar Mage_Page_Block_Html_Pager */
        $toolbar = $this->getLayout()->createBlock('page/html_pager', 'customer_myApplications.toolbar');
        $toolbar->setCollection($this->_collection);
        $this->setChild('toolbar', $toolbar);
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Get collection
     *
     * @return Mage_OAuth_Model_Resource_Token_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get link for update revoke status
     *
     * @param Mage_OAuth_Model_Token $model
     * @return string
     */
    public function getUpdateRevokeLink(Mage_OAuth_Model_Token $model)
    {
        return Mage::getUrl('oauth/myApplication/revoke/',
            array('id' => $model->getId(), 'status' => (int) !$model->getRevoked()));
    }

    /**
     * Get delete link
     *
     * @param Mage_OAuth_Model_Token $model
     * @return string
     */
    public function getDeleteLink(Mage_OAuth_Model_Token $model)
    {
        return Mage::getUrl('oauth/myApplication/delete/', array('id' => $model->getId()));
    }

    /**
     * Get revoke Label by status
     *
     * @param integer $status
     * @return string
     */
    public function getRevokeLabel($status)
    {
        return $this->_revokeLabels[(int) $status];
    }

    /**
     * Get revoke Label by status
     *
     * @return string
     */
    public function getDeleteLabel()
    {
        return $this->__('Delete');
    }
}
