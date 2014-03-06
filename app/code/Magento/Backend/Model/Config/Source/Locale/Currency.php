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
 * Locale currency source
 */
namespace Magento\Backend\Model\Config\Source\Locale;

class Currency implements \Magento\Option\ArrayInterface
{
    /**
     * @var array
     */
    protected $_options;

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
        if (!$this->_options) {
            $this->_options = $this->_localeLists->getOptionCurrencies();
        }
        $options = $this->_options;
        return $options;
    }
}
