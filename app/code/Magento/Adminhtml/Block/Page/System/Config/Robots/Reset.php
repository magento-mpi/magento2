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
 * "Reset to Defaults" button renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Page\System\Config\Robots;

class Reset extends \Magento\Backend\Block\System\Config\Form\Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('page/system/config/robots/reset.phtml');
    }

    /**
     * Get robots.txt custom instruction default value
     *
     * @return string
     */
    public function getRobotsDefaultCustomInstructions()
    {
        return \Mage::helper('Magento\Page\Helper\Robots')->getRobotsDefaultCustomInstructions();
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento\Adminhtml\Block\Widget\Button')
            ->setData(array(
                'id'      => 'reset_to_default_button',
                'label'   => __('Reset to Default'),
                'onclick' => 'javascript:resetRobotsToDefault(); return false;'
            ));

        return $button->toHtml();
    }

    /**
     * Render button
     *
     * @param  \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Data\Form\Element\AbstractElement $element)
    {
        // Remove scope label
        $element->unsScope()->unsCanUseWebsiteValue()->unsCanUseDefaultValue();
        return parent::render($element);
    }

    /**
     * Return element html
     *
     * @param  \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        return $this->_toHtml();
    }
}
