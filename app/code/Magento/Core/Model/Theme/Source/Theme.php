<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for eav attribute custom_design
 */
namespace Magento\Core\Model\Theme\Source;

use Magento\Framework\View\Design\Theme\Label;
use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Design
 *
 */
class Theme extends AbstractSource
{
    /**
     * @var Label
     */
    protected $themeLabel;

    /**
     * @param Label $themeLabel
     */
    public function __construct(Label $themeLabel)
    {
        $this->themeLabel = $themeLabel;
    }

    /**
     * Retrieve All Design Theme Options
     *
     * @param bool $withEmpty add empty (please select) values to result
     * @return Label[]
     */
    public function getAllOptions($withEmpty = true)
    {
        $label = $withEmpty ? __('-- Please Select --') : $withEmpty;
        return $this->_options = $this->themeLabel->getLabelsCollection($label);
    }
}
