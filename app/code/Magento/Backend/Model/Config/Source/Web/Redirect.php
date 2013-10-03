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

class Redirect implements \Magento\Core\Model\Option\ArrayInterface
{

    public function toOptionArray()
    {
        return array(
            array('value' => 0, 'label'=>__('No')),
            array('value' => 1, 'label'=>__('Yes (302 Found)')),
            array('value' => 301, 'label'=>__('Yes (301 Moved Permanently)')),
        );
    }

}
