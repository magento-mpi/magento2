<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Backend\Model\Config\Source\Design;

class Robots implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'INDEX,FOLLOW', 'label'=>'INDEX, FOLLOW'),
            array('value'=>'NOINDEX,FOLLOW', 'label'=>'NOINDEX, FOLLOW'),
            array('value'=>'INDEX,NOFOLLOW', 'label'=>'INDEX, NOFOLLOW'),
            array('value'=>'NOINDEX,NOFOLLOW', 'label'=>'NOINDEX, NOFOLLOW'),
        );
    }
}
