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

class SkipList
    extends \Magento\Backend\Block\Widget\Grid\Column\Filter\AbstractFilter
{
    public function getCondition()
    {
        return array('nin' => $this->getValue() ?: array(0));
    }
}
