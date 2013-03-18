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
class Enterprise_Pbridge_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Class constructor. Initialize checkout session namespace
     */
    public function __construct()
    {
        $this->init('enterprise_pbridge');
    }
}
