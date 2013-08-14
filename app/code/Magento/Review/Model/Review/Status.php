<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review status
 *
 * @category   Magento
 * @package    Magento_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Review_Model_Review_Status extends Magento_Core_Model_Abstract
{

    public function __construct()
    {
        $this->_init('Magento_Review_Model_Resource_Review_Status');
    }
}
