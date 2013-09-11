<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Source\Date;

class Short implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        $arr = array();
        $arr[] = array('label'=>'', 'value'=>'');
        $arr[] = array('label'=>strftime('MM/DD/YY (%m/%d/%y)'), 'value'=>'%m/%d/%y');
        $arr[] = array('label'=>strftime('MM/DD/YYYY (%m/%d/%Y)'), 'value'=>'%m/%d/%Y');
        $arr[] = array('label'=>strftime('DD/MM/YY (%d/%m/%y)'), 'value'=>'%d/%m/%y');
        $arr[] = array('label'=>strftime('DD/MM/YYYY (%d/%m/%Y)'), 'value'=>'%d/%m/%Y');
        return $arr;
    }
}
