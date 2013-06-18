<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_Limitation_Model_Limitation_Config
{
    /**
     * XPath to configuration node that specifies limitation threshold value
     */
    const XML_PATH_THRESHOLD = 'limitations/%s';

    /**
     * @var Mage_Core_Model_Config
     */
    private $_config;

    /**
     * @param Mage_Core_Model_Config $config
     */
    public function __construct(Mage_Core_Model_Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Retrieve threshold value by a limitation identifier
     *
     * @param string $identifier
     * @return int
     */
    public function getThreshold($identifier)
    {
        return (int)$this->_config->getNode(sprintf(self::XML_PATH_THRESHOLD, $identifier));
    }
}
