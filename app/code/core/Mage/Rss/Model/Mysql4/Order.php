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
 * @category   Mage
 * @package    Mage_Rss
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Quote rule mysql4 resource model
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Lindy Kyaw (lindy@varien.com)
 */
class Mage_Rss_Model_Mysql4_Order
{
    public function getCoreResource()
    {
        return Mage::getSingleton('core/resource');
    }

	public function getAllOrderEntityIds($oid)
	{
	    $res = $this->getCoreResource();
	    $read = $res->getConnection('core_read');
	    $select = $read->select()
             ->from($res->getTableName('sales/order'), array('entity_id'))
             ->where('parent_id=?', $oid)
             ->orWhere('entity_id=?', $oid);
        $results = $read->fetchAll($select);
        $eIds = array();
        foreach($results as $result){
            $eIds[] = $result['entity_id'];
        }
        return $eIds;
	}

	public function getAllEntityTypeIds()
	{
	    $entityTypes = array();
        $eav = Mage::getSingleton('eav/config');
        foreach (array('order_status_history', 'invoice_comment', 'shipment_comment', 'creditmemo_comment') as $entityTypeCode) {
            $entityType = $eav->getEntityType($entityTypeCode);
            $entityTypes[$entityType->getId()] = array(
                'table'=>$entityType->getEntityTable(),
                'comment_attribute_id'=>$eav->getAttribute($entityType, 'comment')->getId(),
                'notified_attribute_id'=>$eav->getAttribute($entityType, 'is_customer_notified')->getId(),
            );
        }
        return $entityTypes;
	}


}