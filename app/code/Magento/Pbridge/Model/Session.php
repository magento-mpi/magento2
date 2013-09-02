<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Pbridge_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * Initialize Enterprise Pbridge session namespace
     *
     * @param Magento_Core_Helper_Http $coreHttp
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(
        Magento_Core_Helper_Http $coreHttp,
        array $data = array(),
        $sessionName = null
    ) {
        parent::__construct($coreHttp, $data);
        $this->init('magento_pbridge', $sessionName);
    }
}
