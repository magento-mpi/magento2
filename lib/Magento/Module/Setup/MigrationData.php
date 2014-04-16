<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Module
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Module\Setup;

/**
 * Replace patterns needed for migration process between Magento versions
 * @SuppressWarnings(PHPMD.ExcessiveParameterList)
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class MigrationData
{
    /**
     * List of required params
     *
     * @var string[]
     */
    protected $_requiredParams = array('plain', 'wiki', 'xml', 'serialized');

    /**
     * List of replace patterns
     *
     * @var string[]
     */
    protected $_patterns = array();

    /**
     * @param array $data
     * @throws \InvalidArgumentException
     */
    public function __construct(
        array $data
    ) {
        foreach ($this->_requiredParams as $param) {
            if (!isset($data[$param])) {
                throw new \InvalidArgumentException("Missing required param " . $param);
            }
            $this->_patterns[$param] = $data[$param];
        }
    }

    /**
     * Get replace pattern
     *
     * @return string
     */
    public function getPlainFindPattern()
    {
        return $this->_patterns['plain'];
    }

    /**
     * Get replace pattern
     *
     * @return string
     */
    public function getWikiFindPattern()
    {
        return $this->_patterns['wiki'];
    }

    /**
     * Get replace pattern
     *
     * @return string
     */
    public function getXmlFindPattern()
    {
        return $this->_patterns['xml'];
    }

    /**
     * Get replace pattern
     *
     * @return string
     */
    public function getSerializedFindPattern()
    {
        return $this->_patterns['serialized'];
    }
}
