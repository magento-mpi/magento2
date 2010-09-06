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
 * @package     Mage_XmlConnect
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
/**
 * Enter description here ...
 *
 * @method Mage_XmlConnect_Model_Resource_History _getResource()
 * @method Mage_XmlConnect_Model_Resource_History getResource()
 * @method Mage_XmlConnect_Model_History getApplicationId()
 * @method int setApplicationId(int $value)
 * @method Mage_XmlConnect_Model_History getCreatedAt()
 * @method string setCreatedAt(string $value)
 * @method Mage_XmlConnect_Model_History getStoreId()
 * @method int setStoreId(int $value)
 * @method Mage_XmlConnect_Model_History getParams()
 * @method string setParams(string $value)
 * @method Mage_XmlConnect_Model_History getTitle()
 * @method string setTitle(string $value)
 * @method Mage_XmlConnect_Model_History getActivationKey()
 * @method string setActivationKey(string $value)
 * @method Mage_XmlConnect_Model_History getCode()
 * @method string setCode(string $value)
 *
 * @category    Mage
 * @package     Mage_XmlConnect
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_History extends Mage_Core_Model_Abstract
{
    /**
     * Initialize application
     */
    protected function _construct()
    {
        $this->_init('xmlconnect/history');
    }

    /**
     * Get array of existing images
     *
     * @param int $id application instance Id
     * @return array
     */
    public function getLastParams($id)
    {
        return $this->_getResource()->getLastParams($id);
    }
}
