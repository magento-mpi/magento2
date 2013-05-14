<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Rating vote model
 *
 * @category   Mage
 * @package    Mage_Rating
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Rating_Model_Rating_Option_Vote extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('Mage_Rating_Model_Resource_Rating_Option_Vote');
    }
}
