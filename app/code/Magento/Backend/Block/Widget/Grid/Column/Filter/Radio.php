<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

/**
 * Checkbox grid column filter
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Radio extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    /**
     * @return array
     */
    protected function _getOptions()
    {
        return array(
            array('label' => __('Any'), 'value' => ''),
            array('label' => __('Yes'), 'value' => 1),
            array('label' => __('No'), 'value' => 0)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getCondition()
    {
        if ($this->getValue()) {
            return $this->getColumn()->getValue();
        } else {
            return array(array('neq' => $this->getColumn()->getValue()), array('is' => new \Zend_Db_Expr('NULL')));
        }
    }
}
