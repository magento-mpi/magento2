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
namespace Magento\Oauth\Block\Customer\Token;

class ListToken extends \Magento\Customer\Block\Account\Dashboard
{
    /**
     * Collection model
     *
     * @var \Magento\Oauth\Model\Resource\Token\Collection
     */
    protected $_collection;

    /**
     * @var \Magento\Oauth\Model\TokenFactory
     */
    protected $tokenFactory;

    /**
     * @param \Magento\Oauth\Model\TokenFactory $tokenFactory
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Oauth\Model\TokenFactory $tokenFactory,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Newsletter\Model\SubscriberFactory $subscriberFactory,
        array $data = array()
    ) {
        $this->tokenFactory = $tokenFactory;
        parent::__construct($coreData, $context, $customerSession, $subscriberFactory, $data);
    }

    /**
     * Prepare collection
     */
    protected function _construct()
    {
        /** @var $collection \Magento\Oauth\Model\Resource\Token\Collection */
        $collection = $this->tokenFactory->create()->getCollection();
        $collection->joinConsumerAsApplication()
                ->addFilterByType(\Magento\Oauth\Model\Token::TYPE_ACCESS)
                ->addFilterByCustomerId($this->_customerSession->getCustomerId());
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
     * @return \Magento\Oauth\Block\Customer\Token\ListToken
     */
    protected function _prepareLayout()
    {
        /** @var $toolbar \Magento\Page\Block\Html\Pager */
        $toolbar = $this->getLayout()->createBlock('Magento\Page\Block\Html\Pager', 'customer_token.toolbar');
        $toolbar->setCollection($this->_collection);
        $this->setChild('toolbar', $toolbar);
        parent::_prepareLayout();
        return $this;
    }

    /**
     * Get collection
     *
     * @return \Magento\Oauth\Model\Resource\Token\Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * Get link for update revoke status
     *
     * @param \Magento\Oauth\Model\Token $model
     * @return string
     */
    public function getUpdateRevokeLink(\Magento\Oauth\Model\Token $model)
    {
        return $this->_urlBuilder->getUrl('oauth/customer_token/revoke/',
            array('id' => $model->getId(), 'status' => (int) !$model->getRevoked()));
    }

    /**
     * Get delete link
     *
     * @param \Magento\Oauth\Model\Token $model
     * @return string
     */
    public function getDeleteLink(\Magento\Oauth\Model\Token $model)
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
