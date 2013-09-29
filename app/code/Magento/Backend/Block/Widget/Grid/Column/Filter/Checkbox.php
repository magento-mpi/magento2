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
 * Checkbox grid column filter
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Backend\Block\Widget\Grid\Column\Filter;

class Checkbox extends \Magento\Backend\Block\Widget\Grid\Column\Filter\Select
{
    public function getHtml()
    {
        return '<span class="head-massaction">' . parent::getHtml() . '</span>';
    }

    protected function _getOptions()
    {
        return array(
            array(
                'label' => __('Any'),
                'value' => ''
            ),
            array(
                'label' => __('Yes'),
                'value' => 1
            ),
            array(
                'label' => __('No'),
                'value' => 0
            ),
        );
    }

    public function getCondition()
    {
        if ($this->getValue()) {
            return $this->getColumn()->getValue();
        } else {
            return array(
                array('neq'=>$this->getColumn()->getValue()),
                array('is'=>new \Zend_Db_Expr('NULL'))
            );
        }
        //return array('like'=>'%'.$this->getValue().'%');
    }
}
