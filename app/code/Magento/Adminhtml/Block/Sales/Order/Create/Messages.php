<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Order create errors block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Create;

class Messages extends \Magento\Adminhtml\Block\Messages
{
    /**
     * @var \Magento\Adminhtml\Model\Session\Quote
     */
    protected $_sessionQuote;

    /**
     * @param \Magento\Adminhtml\Model\Session\Quote $sessionQuote
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Adminhtml\Model\Session\Quote $sessionQuote,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_sessionQuote = $sessionQuote;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * @return \Magento\Core\Block\Messages
     */
    protected function _prepareLayout()
    {
        $this->addMessages($this->_sessionQuote->getMessages(true));
        parent::_prepareLayout();
    }

}
