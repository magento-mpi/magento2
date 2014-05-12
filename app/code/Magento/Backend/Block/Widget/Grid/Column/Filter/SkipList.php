<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

/**
 * Massaction grid column filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class SkipList extends \Magento\Backend\Block\Widget\Grid\Column\Filter\AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        return array('nin' => $this->getValue() ?: array(0));
    }
}
