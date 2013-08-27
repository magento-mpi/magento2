<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * Initialize Enterprise Pbridge session namespace
     *
     * @param string $sessionName
     */
    public function __construct($sessionName = null)
    {
        $this->init('enterprise_pbridge', $sessionName);
    }
}
