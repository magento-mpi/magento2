<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Newsletter
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Newsletter Data Helper
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Newsletter\Helper;

class Data extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * Url
     *
     * @var \Magento\Core\Model\UrlInterface
     */
    protected $_url;

    /**
     * Constructor
     *
     * @param \Magento\Core\Helper\Context $context
     * @param \Magento\Core\Model\UrlInterface $url
     */
    public function __construct(
        \Magento\Core\Helper\Context $context,
        \Magento\Core\Model\UrlInterface $url
    ) {
        parent::__construct($context);
        $this->_url = $url;
    }

    /**
     * Retrieve subsription confirmation url
     *
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return string
     */
    public function getConfirmationUrl($subscriber)
    {
        return $this->_url->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/confirm', array(
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true,
            ));
    }

    /**
     * Retrieve unsubsription url
     *
     * @param \Magento\Newsletter\Model\Subscriber $subscriber
     * @return string
     */
    public function getUnsubscribeUrl($subscriber)
    {
        return $this->_url->setStore($subscriber->getStoreId())
            ->getUrl('newsletter/subscriber/unsubscribe', array(
                'id'     => $subscriber->getId(),
                'code'   => $subscriber->getCode(),
                '_nosid' => true,
            ));
    }
}
