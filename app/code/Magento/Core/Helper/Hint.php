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
class Magento_Core_Helper_Hint extends Magento_Core_Helper_Abstract
{
    /**
     * List of available hints
     *
     * @var null|array
     */
    protected $_availableHints;

    /**
     * Retrieve list of available hints as [hint code] => [hint url]
     *
     * @return array
     */
    public function getAvailableHints()
    {
        if (null === $this->_availableHints) {
            $hints = array();
            $config = Mage::getConfig()->getValue('hints', 'default');
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
