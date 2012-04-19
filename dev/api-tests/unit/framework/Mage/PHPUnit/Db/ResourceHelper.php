<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Local resource helper.
 * Need to disable all functionality for local database adapter
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_ResourceHelper extends Mage_Core_Model_Resource_Helper_Abstract
{
    public function addLikeEscape($value, $options = array())
    {
    }
}
