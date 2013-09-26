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
 * Newsletter subscribe block
 *
 * @category   Magento
 * @package    Magento_Newsletter
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Newsletter\Block;

class Subscribe extends \Magento\Core\Block\Template
{
    /**
     * Newsletter session
     *
     * @var \Magento\Newsletter\Model\Session
     */
    protected $_newsletterSession;

    /**
     * Construct
     *
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Newsletter\Model\Session $newsletterSession
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Newsletter\Model\Session $newsletterSession,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_newsletterSession = $newsletterSession;
    }

    public function getSuccessMessage()
    {
        return $this->_newsletterSession->getSuccess();
    }

    public function getErrorMessage()
    {
        return $this->_newsletterSession->getError();
    }

    /**
     * Retrieve form action url and set "secure" param to avoid confirm
     * message when we submit form from secure page to unsecure
     *
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('newsletter/subscriber/new', array('_secure' => true));
    }
}
