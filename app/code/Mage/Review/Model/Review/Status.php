<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Review
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review status
 *
 * @category   Mage
 * @package    Mage_Review
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Review_Model_Review_Status extends Magento_Core_Model_Abstract
{

    public function __construct()
    {
        $this->_init('Mage_Review_Model_Resource_Review_Status');
    }
}
