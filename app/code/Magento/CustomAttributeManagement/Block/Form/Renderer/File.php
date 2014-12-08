<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomAttributeManagement\Block\Form\Renderer;

use Magento\Framework\View\Element\Template;

/**
 * EAV Entity Attribute Form Renderer Block for File
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class File extends \Magento\CustomAttributeManagement\Block\Form\Renderer\AbstractRenderer
{
    /**
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param Template\Context $context
     * @param array $data
     */
    public function __construct(Template\Context $context, \Magento\Core\Helper\Data $coreData, array $data = [])
    {
        $this->_coreData = $coreData;
        parent::__construct($context, $data);
    }

    /**
     * Return escaped value
     *
     * @return string
     */
    public function getEscapedValue()
    {
        if ($this->getValue()) {
            return $this->escapeHtml($this->_coreData->urlEncode($this->getValue()));
        }
        return '';
    }
}
