<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Oauth
 * @copyright  {copyright}
 * @license    {license_link}
 */


/**
 * Customer My Applications list block
 *
 * @category   Magento
 * @package    Magento_Oauth
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Oauth_Block_Customer_Token_List extends Magento_Customer_Block_Account_Dashboard
{
    /**
     * Collection model
     *
     * @var Magento_Oauth_Model_Resource_Token_Collection
     */
    protected $_collection;

    /**
     * Token Collection factory
     *
     * @var Magento_Oauth_Model_Resource_Token_CollectionFactory
     */
    protected $_tokenColFactory = null;

    /**
     * Token Collection factory
     *
     * @var Magento_Customer_Model_Session
     */
    protected $_customerSession = null;

    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Customer_Model_Session $customerSession,
        Magento_Oauth_Model_Resource_Token_CollectionFactory $tokenColFactory,
        array $data = array()
    ) {
        $this->_customerSession = $customerSession;
        $this->_tokenColFactory = $tokenColFactory;
        parent::__construct($coreData, $context, $data);
    }


    /**
     * Prepare collection
     */
    protected function _construct()
    {
        /** @var $session Magento_Customer_Model_Session */
        $session = $this->_customerSession;

        /** @var $collection Magento_Oauth_Model_Resource_Token_Collection */
        $collection = $this->_tokenColFactory->create();
        $collection->joinConsumerAsApplication()
                ->addFilterByType(Magento_Oauth_Model_Token::TYPE_ACCESS)
                ->addFilterByCustomerId($session->getCustomerId());
        $this->_collection = $collection;
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
     * @return Magento_Oauth_Block_Customer_Token_List
     */
    protected function _prepareLayout()
    {
        /** @var $toolbar Magento_Page_Block_Html_Pager */
        $toolbar = $this->getLayout()->createBlock('Magento_Page_Block_Html_Pager', 'customer_token.toolbar');
        $toolbar->setCollection($this->_collection);
        $this->setChild('toolbar', $toolbar);
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Get collection
     *
     * @return Magento_Oauth_Model_Resource_Token_Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get link for update revoke status
     *
     * @param Magento_Oauth_Model_Token $model
     * @return string
     */
    public function getUpdateRevokeLink(Magento_Oauth_Model_Token $model)
    {
        return $this->_urlBuilder->getUrl('oauth/customer_token/revoke/',
            array('id' => $model->getId(), 'status' => (int) !$model->getRevoked()));
    }

    /**
     * Get delete link
     *
     * @param Magento_Oauth_Model_Token $model
     * @return string
     */
    public function getDeleteLink(Magento_Oauth_Model_Token $model)
    {
        return $this->_urlBuilder->getUrl('oauth/customer_token/delete/', array('id' => $model->getId()));
    }

    /**
     * Retrieve a token status label
     *
     * @param int $revokedStatus Token status of revoking
     * @return string
     */
    public function getStatusLabel($revokedStatus)
    {
        $labels = array(
            __('Enabled'),
            __('Disabled')
        );
        return $labels[$revokedStatus];
    }

    /**
     * Retrieve a label of link to change a token status
     *
     * @param int $revokedStatus Token status of revoking
     * @return string
     */
    public function getChangeStatusLabel($revokedStatus)
    {
        $labels = array(
            __('Disable'),
            __('Enable')
        );
        return $labels[$revokedStatus];
    }

    /**
     * Retrieve a message to confirm an action to change a token status
     *
     * @param int $revokedStatus Token status of revoking
     * @return string
     */
    public function getChangeStatusConfirmMessage($revokedStatus)
    {
        $messages = array(
            __('Are you sure you want to disable this application?'),
            __('Are you sure you want to enable this application?')
        );
        return $messages[$revokedStatus];
    }
}
