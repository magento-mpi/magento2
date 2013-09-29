<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for eav attribute custom_design
 */
namespace Magento\Core\Model\Design\Source;

class Design extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magento\Core\Model\Theme\Label
     */
    protected $_themeLabel;

    /**
     * @param \Magento\Core\Model\Theme\Label $themeLabel
     */
    public function __construct(\Magento\Core\Model\Theme\Label $themeLabel)
    {
        $this->_themeLabel = $themeLabel;
    }

    /**
     * Retrieve All Design Theme Options
     *
     * @param bool $withEmpty add empty (please select) values to result
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        $label = $withEmpty ? __('-- Please Select --') : $withEmpty;
        return $this->_options = $this->_themeLabel->getLabelsCollection($label);
    }
}
