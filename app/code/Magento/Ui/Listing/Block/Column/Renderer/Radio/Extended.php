<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column\Renderer\Radio;

class Extended extends \Magento\Ui\Listing\Block\Column\Renderer\Radio
{
    /**
     * Prepare data for renderer
     *
     * @return array
     */
    protected function _getValues()
    {
        return $this->getColumn()->getValues();
    }
}
