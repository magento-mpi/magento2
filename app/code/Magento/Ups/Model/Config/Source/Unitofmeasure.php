<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ups\Model\Config\Source;

/**
 * Class Unitofmeasure
 */
class Unitofmeasure extends \Magento\Ups\Model\Config\Source\Generic
{
    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = 'unit_of_measure';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $unitArr = $this->carrierConfig->getCode($this->_code);
        $returnArr = array();
        foreach ($unitArr as $key => $val) {
            $returnArr[] = array('value' => $key, 'label' => $key);
        }
        return $returnArr;
    }
}
