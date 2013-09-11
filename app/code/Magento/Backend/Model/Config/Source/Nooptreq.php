<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source;

class Nooptreq implements \Magento\Core\Model\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>__('No')),
            array('value'=>'opt', 'label'=>__('Optional')),
            array('value'=>'req', 'label'=>__('Required')),
        );
    }

}
