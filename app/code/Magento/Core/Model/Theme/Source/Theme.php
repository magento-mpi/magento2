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
namespace Magento\Core\Model\Theme\Source;

use Magento\View\Design\Theme\Label;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Design
 *
 * @package Magento\View
 */
class Theme extends AbstractSource
{
    /**
     * @var \Magento\View\Design\Theme\Label
     */
    protected $themeLabel;

    /**
     * @param \Magento\View\Design\Theme\Label $themeLabel
     */
    public function __construct(Label $themeLabel)
    {
        $this->themeLabel = $themeLabel;
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
        return $this->_options = $this->themeLabel->getLabelsCollection($label);
    }
}
