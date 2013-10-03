<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Model\Config\Source\Web;

class Protocol implements \Magento\Core\Model\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return array(
            array('value'=>'', 'label'=>''),
            array('value'=>'http', 'label'=>__('HTTP (unsecure)')),
            array('value'=>'https', 'label'=>__('HTTPS (SSL)')),
        );
    }

}
