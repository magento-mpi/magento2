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
     * @param Magento_Core_Model_Session_Validator $validator
     * @param string $sessionName
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Session_Validator $validator,
        $sessionName = null,
        array $data = array()
    ) {
        parent::__construct($validator, $data);
        $this->init('magento_pbridge', $sessionName);
    }
}
