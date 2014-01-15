<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * "Reset to Defaults" button renderer
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Page\System\Config\Robots;

class Reset extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * Pasge robots default instructions
     */
    const XML_PATH_ROBOTS_DEFAULT_CUSTOM_INSTRUCTIONS = 'design/search_engine_robots/default_custom_instructions';

    /**
     * Page robots
     *
     * @var \Magento\Theme\Helper\Robots
     */
    protected $coreConfig;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\ConfigInterface $coreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\ConfigInterface $coreConfig,
        array $data = array()
    ) {
        $this->coreConfig = $coreConfig;
        parent::__construct($context, $data);
    }

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
        return trim((string)$this->coreConfig->getValue(self::XML_PATH_ROBOTS_DEFAULT_CUSTOM_INSTRUCTIONS, 'default'));
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('Magento\Backend\Block\Widget\Button')
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
