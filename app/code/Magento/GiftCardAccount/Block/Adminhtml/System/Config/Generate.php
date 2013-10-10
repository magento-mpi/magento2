<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCardAccount
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\GiftCardAccount\Block\Adminhtml\System\Config;

class Generate
    extends \Magento\Backend\Block\System\Config\Form\Field
{

    protected $_template = 'config/generate.phtml';

    /**
     * Pool factory
     *
     * @var \Magento\GiftCardAccount\Model\Pool\Factory
     */
    protected $_poolFactory;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\App $application
     * @param \Magento\GiftCardAccount\Model\Pool\Factory $poolFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\App $application,
        \Magento\GiftCardAccount\Model\Pool\Factory $poolFactory,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $application, $data);
        $this->_poolFactory = $poolFactory;
    }

    /**
     * Get the button and scripts contents
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->_toHtml();
    }

    /**
     * Return code pool usage
     *
     * @return \Magento\Object
     */
    public function getUsage()
    {
        return $this->_poolFactory->create()->getPoolUsageInfo();
    }
}
