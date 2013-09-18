<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Core hint helper
 *
 * @category    Magento
 * @package     Magento_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Core\Helper;

class Hint extends \Magento\Core\Helper\AbstractHelper
{
    /**
     * List of available hints
     *
     * @var null|array
     */
    protected $_availableHints;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Helper_Context $context
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Helper_Context $context,
        Magento_Core_Model_Config $coreConfig
    ) {
        parent::__construct(
            $context
        );
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Retrieve list of available hints as [hint code] => [hint url]
     *
     * @return array
     */
    public function getAvailableHints()
    {
        if (null === $this->_availableHints) {
            $hints = array();
            $config = $this->_coreConfig->getValue('hints', 'default');
            if ($config) {
                foreach ($config as $type => $configValue) {
                    if (isset($configValue['enabled']) && $configValue['enabled']) {
                        $hints[$type] = $configValue['url'];
                    }
                }
            }
            $this->_availableHints = $hints;
        }
        return $this->_availableHints;
    }

    /**
     * Get Hint Url by Its Code
     *
     * @param string $code
     * @return null|string
     */
    public function getHintByCode($code)
    {
        $hint = null;
        $hints = $this->getAvailableHints();
        if (array_key_exists($code, $hints)) {
            $hint = $hints[$code];
        }
        return $hint;
    }
}
