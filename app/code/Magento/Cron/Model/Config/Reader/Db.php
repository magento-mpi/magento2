<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cron\Model\Config\Reader;

/**
 * Reader for cron parameters from data base storage
 */
class Db
{
    /**
     * Converter instance
     *
     * @var \Magento\Cron\Model\Config\Converter\Db
     */
    protected $_converter;

    /**
     * @var \Magento\Core\Model\Config\Section\Reader\DefaultReader
     */
    protected $_defaultReader;

    /**
     * Initialize parameters
     *
     * @param \Magento\Core\Model\Config\Section\Reader\DefaultReader $defaultReader
     * @param \Magento\Cron\Model\Config\Converter\Db                 $converter
     */
    public function __construct(
        \Magento\Core\Model\Config\Section\Reader\DefaultReader $defaultReader,
        \Magento\Cron\Model\Config\Converter\Db $converter
    ) {
        $this->_defaultReader = $defaultReader;
        $this->_converter = $converter;
    }

    /**
     * Return converted data
     *
     * @return array
     */
    public function get()
    {
        return $this->_converter->convert($this->_defaultReader->read());
    }
}
