<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ui\Listing\Block\Column\Filter;

/**
 * Massaction grid column filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Massaction extends \Magento\Ui\Listing\Block\Column\Filter\Checkbox
{
    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        if ($this->getValue()) {
            return array('in' => $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0));
        } else {
            return array('nin' => $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0));
        }
    }
}
