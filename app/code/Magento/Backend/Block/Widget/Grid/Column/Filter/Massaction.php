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
 * Massaction grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

class Massaction extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Checkbox
{
    /**
     * Set title to proper select rendering
     *
     * @return string
     */
    public function getHtml()
    {
        $this->getColumn()->setTitle(__('Massaction selection'));
        return parent::getHtml();
    }

    public function getCondition()
    {
        if ($this->getValue()) {
            return array('in'=> ( $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0) ));
        } else {
            return array('nin'=> ( $this->getColumn()->getSelected() ? $this->getColumn()->getSelected() : array(0) ));
        }
    }
}
