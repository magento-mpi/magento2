<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCardAccount\Block\Adminhtml\System\Config;

class Generate extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * @var string
     */
    protected $_template = 'config/generate.phtml';

    /**
     * Pool factory
     *
     * @var \Magento\GiftCardAccount\Model\PoolFactory
     */
    protected $_poolFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\GiftCardAccount\Model\PoolFactory $poolFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\GiftCardAccount\Model\PoolFactory $poolFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_poolFactory = $poolFactory;
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Return code pool usage
     *
     * @return \Magento\Framework\Object
     */
    public function getUsage()
    {
        return $this->_poolFactory->create()->getPoolUsageInfo();
    }
}
