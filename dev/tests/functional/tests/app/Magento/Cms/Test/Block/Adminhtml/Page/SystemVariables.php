<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cms\Test\Block\Adminhtml\Page;

use Mtf\Block\Block;

/**
 * Class SystemVariables
 * System variables manage block
 */
class SystemVariables extends Block
{
    /**
     * Returns array with all variables
     *
     * @return array $values
     */
    public function getAllVariables()
    {
        $values = [];

        $variableElements = $this->_rootElement->find('.insert-variable > li > a')->getElements();
        foreach ($variableElements as $variableElement) {
            if ($variableElement->isVisible()) {
                $values[] = $variableElement->getText();
            }
        }

        return $values;
    }
}
