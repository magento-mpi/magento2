<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @method Mage_Eav_Model_Resource_Entity_Store _getResource()
 * @method Mage_Eav_Model_Resource_Entity_Store getResource()
 * @method Mage_Eav_Model_Entity_Store getEntityTypeId()
 * @method int setEntityTypeId(int $value)
 * @method Mage_Eav_Model_Entity_Store getStoreId()
 * @method int setStoreId(int $value)
 * @method Mage_Eav_Model_Entity_Store getIncrementPrefix()
 * @method string setIncrementPrefix(string $value)
 * @method Mage_Eav_Model_Entity_Store getIncrementLastId()
 * @method string setIncrementLastId(string $value)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Entity_Store extends Mage_Core_Model_Abstract
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('eav/entity_store');
    }

    /**
     * Load entity by store
     *
     * @param int $entityTypeId
     * @param int $storeId
     * @return Mage_Eav_Model_Entity_Store
     */
    public function loadByEntityStore($entityTypeId, $storeId)
    {
        $this->_getResource()->loadByEntityStore($this, $entityTypeId, $storeId);
        return $this;
    }
}
