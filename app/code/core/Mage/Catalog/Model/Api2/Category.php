<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * API2 category resource
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Category extends Mage_Api2_Model_Resource
{
    protected function _getResourceAttributes()
    {
        $onlyVisible = (Mage_Api2_Model_Auth_User_Admin::USER_TYPE != $this->getUserType());
        return $this->getEavAttributes($onlyVisible, true);
    }
}
