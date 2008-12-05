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
 * @category   Mage
 * @package    Mage_Downloadable
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable Product  Samples resource model
 *
 * @category   Mage
 * @package    Mage_Downloadable
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Mysql4_Sample extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Varien class constructor
     *
     */
    protected function  _construct()
    {
        $this->_init('downloadable/sample', 'sample_id');
    }

    /**
     * Save title of sample item in store scope
     *
     * @param Mage_Downloadable_Model_Sample $sampleObject
     * @return Mage_Downloadable_Model_Mysql4_Sample
     */
    public function saveItemTitle($sampleObject)
    {
        $stmt = $this->_getReadAdapter()->select()
            ->from($this->getTable('downloadable/sample_title'))
            ->where('sample_id = ?', $sampleObject->getId())
            ->where('store_id = ?', $sampleObject->getStoreId());
        if ($this->_getReadAdapter()->fetchOne($stmt)) {
            $this->_getWriteAdapter()->update(
                $this->getTable('downloadable/sample_title'),
                array(
                    'title' => $sampleObject->getTitle(),
                ),
                $this->_getReadAdapter()->quoteInto('sample_id = ?', $sampleObject->getId()) .
                    ' AND ' .
                    $this->_getReadAdapter()->quoteInto('store_id = ?', $sampleObject->getStoreId()));
        } else {
            $this->_getWriteAdapter()->insert(
                $this->getTable('downloadable/sample_title'),
                array(
                    'sample_id' => $sampleObject->getId(),
                    'store_id' => $sampleObject->getStoreId(),
                    'title' => $sampleObject->getTitle(),
                ));
        }
        return $this;
    }

}
