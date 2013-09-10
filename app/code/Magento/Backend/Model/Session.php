<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Auth session model
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Backend_Model_Session extends Magento_Core_Model_Session_Abstract
{
    /**
     * @param Magento_Core_Model_Session_Validator $validator
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Session_Validator $validator,
        array $data = array()
    ) {
        parent::__construct($validator, $data);
        $this->init('adminhtml');
    }
}
