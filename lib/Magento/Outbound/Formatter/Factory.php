<?php
/**
 * Factory implementation for the PubSub_FormatterInterface
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Outbound
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Outbound_Formatter_Factory
{
    /**
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array representing the map for formats and formatter classes
     */
    protected $_formatterMap = array();

    /**
     * @param array $formatterMap
     * @param Magento_ObjectManager $objectManager
     */
    public function __construct(
        array $formatterMap,
        Magento_ObjectManager $objectManager
    ) {
        $this->_formatterMap = $formatterMap;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get formatter for specified format
     *
     * @param string $format
     * @return Magento_Outbound_FormatterInterface
     * @throws LogicException
     */
    public function getFormatter($format)
    {
        if (!isset($this->_formatterMap[$format])) {
            throw new LogicException("There is no formatter for the format given: {$format}");
        }
        $formatterClassName = $this->_formatterMap[$format];

        $formatter =  $this->_objectManager->get($formatterClassName);
        if (!$formatter instanceof Magento_Outbound_FormatterInterface) {
            throw new LogicException("Formatter class for {$format} does not implement FormatterInterface.");
        }
        return $formatter;
    }

}
