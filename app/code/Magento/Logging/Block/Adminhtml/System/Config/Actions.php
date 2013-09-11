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

class Actions
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    protected $_template = 'system/config/actions.phtml';

    /**
     * Action group labels getter
     *
     * @return array
     */
    public function getLabels()
    {
        return \Mage::getSingleton('Magento\Logging\Model\Config')->getLabels();
    }

    /**
     * Check whether specified group is active
     *
     * @param string $key
     * @return bool
     */
    public function getIsChecked($key)
    {
        return \Mage::getSingleton('Magento\Logging\Model\Config')->isActive($key, true);
    }

    /**
     * Render element html
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        $this->setNamePrefix($element->getName())
            ->setHtmlId($element->getHtmlId());
        return $this->_toHtml();
    }
}
