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
namespace Magento\Outbound\Formatter;

use Magento\ObjectManager;
use Magento\Outbound\FormatterInterface;

class Factory
{
    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @var array representing the map for formats and formatter classes
     */
    protected $_formatterMap = array();

    /**
     * @param array $formatterMap
     * @param ObjectManager $objectManager
     */
    public function __construct(array $formatterMap, ObjectManager $objectManager)
    {
        $this->_formatterMap = $formatterMap;
        $this->_objectManager = $objectManager;
    }

    /**
     * Get formatter for specified format
     *
     * @param string $format
     * @return FormatterInterface
     * @throws \LogicException
     */
    public function getFormatter($format)
    {
        if (!isset($this->_formatterMap[$format])) {
            throw new \LogicException("There is no formatter for the format given: {$format}");
        }
        $formatterClassName = $this->_formatterMap[$format];

        $formatter = $this->_objectManager->get($formatterClassName);
        if (!$formatter instanceof FormatterInterface) {
            throw new \LogicException("Formatter class for {$format} does not implement FormatterInterface.");
        }
        return $formatter;
    }
}
