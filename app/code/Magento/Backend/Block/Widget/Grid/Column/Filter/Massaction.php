<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

/**
 * Massaction grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Massaction extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Checkbox
{
    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        if ($this->getValue()) {
            return array('in'=> ( $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0) ));
        } else {
            return array('nin'=> ( $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0) ));
        }
    }
}
