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
class SkipList extends \Magento\Ui\Listing\Block\Column\Filter\AbstractFilter
{
    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        return array('nin' => $this->getValue() ?: array(0));
    }
}
