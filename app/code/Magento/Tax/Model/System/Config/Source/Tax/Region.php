<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Tax\Model\System\Config\Source\Tax;

class Region implements \Magento\Core\Model\Option\ArrayInterface
{
    protected $_options;

    /**
     * @var \Magento\Directory\Model\Resource\Region\Collection\Factory
     */
    protected $_regionsFactory;

    /**
     * @param \Magento\Directory\Model\Resource\Region\Collection\Factory $regionsFactory
     */
    public function __construct(\Magento\Directory\Model\Resource\Region\Collection\Factory $regionsFactory)
    {
        $this->_regionsFactory = $regionsFactory;
    }

    public function toOptionArray($noEmpty = false, $country = null)
    {
        /** @var $region \Magento\Directory\Model\Resource\Region\Collection */
        $regionCollection = $this->_regionsFactory->create();
        $options = $regionCollection->addCountryFilter($country)->toOptionArray();

        if ($noEmpty) {
            unset($options[0]);
        } else {
            if ($options) {
                $options[0]['label'] = '*';
            } else {
                $options = array(array('value' => '', 'label' => '*'));
            }
        }

        return $options;
    }
}
