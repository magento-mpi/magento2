<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column\Renderer;

/**
 * Grid column widget for rendering grid cells that contains mapped values
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Options extends \Magento\Ui\Listing\Block\Column\Renderer\Text
{
    /**
     * Get options from column
     *
     * @return array
     */
    protected function _getOptions()
    {
        return $this->getColumn()->getOptions();
    }

    /**
     * Render a grid cell as options
     *
     * @param \Magento\Framework\Object $row
     * @return string|void
     */
    public function render(\Magento\Framework\Object $row)
    {
        $options = $this->_getOptions();

        $showMissingOptionValues = (bool)$this->getColumn()->getShowMissingOptionValues();
        if (!empty($options) && is_array($options)) {

            //transform option format
            $output = array();
            foreach ($options as $option) {
                $output[$option['value']] = $option['label'];
            }

            $value = $row->getData($this->getColumn()->getIndex());
            if (is_array($value)) {
                $res = array();
                foreach ($value as $item) {
                    if (isset($output[$item])) {
                        $res[] = $this->escapeHtml($output[$item]);
                    } elseif ($showMissingOptionValues) {
                        $res[] = $this->escapeHtml($item);
                    }
                }
                return implode(', ', $res);
            } elseif (isset($output[$value])) {
                return $this->escapeHtml($output[$value]);
            } elseif (in_array($value, $output)) {
                return $this->escapeHtml($value);
            }
        }
    }
}
