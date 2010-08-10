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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Core layout update resource model
 *
 * @category    Mage
 * @package     Mage_Core
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Core_Model_Resource_Layout extends Mage_Core_Model_Resource_Db_Abstract
{
    /**
     * Define main table
     *
     */
    protected function _construct()
    {
        $this->_init('core/layout_update', 'layout_update_id');
    }

    /**
     * Retrieve layout updates by handle
     *
     * @param string $handle
     * @param array $params
     * @return string
     */
    public function fetchUpdatesByHandle($handle, $params = array())
    {
        $storeId = isset($params['store_id']) ? $params['store_id'] : Mage::app()->getStore()->getId();
        $area    = isset($params['area']) ? $params['area'] : Mage::getSingleton('core/design_package')->getArea();
        $package = isset($params['package']) ? $params['package'] : Mage::getSingleton('core/design_package')->getPackageName();
        $theme   = isset($params['theme']) ? $params['theme'] : Mage::getSingleton('core/design_package')->getTheme('layout');

        $result = '';

        $adapter = $this->_getReadAdapter();
        if ($adapter) {
            $select = $adapter->select()
                ->from(array('cl_update' => $this->getMainTable()), array('xml'))
                ->join(
                    array('cl_link' => $this->getTable('core/layout_link')),
                    'cl_link.layout_update_id = cl_update.layout_update_id',
                    array())
                ->where('cl_link.store_id IN (0, ?)', $storeId)
                ->where('cl_link.area=?', $area)
                ->where('cl_link.package=?', $package)
                ->where('cl_link.theme=?', $theme)
                ->where('cl_update.handle = ?', $handle)
                ->order('cl_update.sort_order ASC');

            $result = join('', $adapter->fetchCol($select));
        }

        return $result;
    }
}
