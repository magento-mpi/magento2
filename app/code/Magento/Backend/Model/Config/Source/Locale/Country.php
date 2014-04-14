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
 * Locale country source
 */
namespace Magento\Backend\Model\Config\Source\Locale;

class Country implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Locale\ListsInterface
     */
    protected $_localeLists;

    /**
     * @param \Magento\Locale\ListsInterface $localeLists
     */
    public function __construct(\Magento\Locale\ListsInterface $localeLists)
    {
        $this->_localeLists = $localeLists;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_localeLists->getOptionCountries();
    }
}
