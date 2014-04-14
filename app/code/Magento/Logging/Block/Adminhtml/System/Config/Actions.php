<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Action group checkboxes renderer for system configuration
 */
namespace Magento\Logging\Block\Adminhtml\System\Config;

class Actions extends \Magento\Backend\Block\System\Config\Form\Field
{
    /** @var string */
    protected $_template = 'system/config/actions.phtml';

    /**
     * @var \Magento\Logging\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Logging\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Logging\Model\Config $config,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Action group labels getter
     *
     * @return array
     */
    public function getLabels()
    {
        return $this->_config->getLabels();
    }

    /**
     * Check whether specified group is active
     *
     * @param string $key
     * @return bool
     */
    public function getIsChecked($key)
    {
        return $this->_config->isEventGroupLogged($key);
    }

    /**
     * Render element html
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())->setHtmlId($element->getHtmlId());
        return $this->_toHtml();
    }
}
