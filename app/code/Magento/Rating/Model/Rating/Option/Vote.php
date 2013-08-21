<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating vote model
 *
 * @category   Magento
 * @package    Magento_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Rating_Model_Rating_Option_Vote extends Magento_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Magento_Rating_Model_Resource_Rating_Option_Vote');
    }
}
