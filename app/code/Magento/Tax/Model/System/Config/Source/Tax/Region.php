<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Tax\Model\System\Config\Source\Tax;

class Region implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * @var \Magento\Directory\Model\Resource\Region\CollectionFactory
     */
    protected $_regionsFactory;

    /**
     * @param \Magento\Directory\Model\Resource\Region\CollectionFactory $regionsFactory
     */
    public function __construct(\Magento\Directory\Model\Resource\Region\CollectionFactory $regionsFactory)
    {
        $this->_regionsFactory = $regionsFactory;
    }

    /**
     * Return list of country's regions as array
     *
     * @param bool $noEmpty
     * @param string|array|null $country
     * @return array
     */
    public function toOptionArray($noEmpty = false, $country = null)
    {
        /** @var $region \Magento\Directory\Model\Resource\Region\Collection */
        $regionCollection = $this->_regionsFactory->create();
        $options = $regionCollection->addCountryFilter($country)->toOptionArray();

        if ($noEmpty) {
            unset($options[0]);
        } else {
            if ($options) {
                $options[0] = array('value' => '0', 'label' => '*');
            } else {
                $options = array(array('value' => '0', 'label' => '*'));
            }
        }

        return $options;
    }
}
