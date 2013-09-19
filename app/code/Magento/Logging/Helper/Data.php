<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Logging
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Logging helper
 *
 * @category    Magento
 * @package     Magento_Logging
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Logging_Helper_Data extends Magento_Core_Helper_Abstract
{
    /**
     * @var Magento_Logging_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_Logging_Model_Config $config
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Logging_Model_Config $config,
        Magento_Core_Helper_Context $context
    ) {
        $this->_config = $config;
        parent::__construct($context);
    }

    /**
     * Join array into string except empty values
     *
     * @param array $array Array to join
     * @param string $glue Separator to join
     * @return string
     */
    public function implodeValues($array, $glue = ', ')
    {
        if (!is_array($array)) {
            return $array;
        }
        $result = array();
        foreach ($array as $item) {
            if (is_array($item)) {
                $result[] = $this->implodeValues($item);
            } else {
                if ((string)$item !== '') {
                    $result[] = $item;
                }
            }
        }
        return implode($glue, $result);
    }

    /**
     * Get translated label by logging action name
     *
     * @param string $action
     * @return string
     */
    public function getLoggingActionTranslatedLabel($action)
    {
        return $this->_config->getActionLabel($action);
    }
}
