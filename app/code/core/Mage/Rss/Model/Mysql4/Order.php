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

	public function getAllEntityIds($oid)
	{
	    $res = $this->getCoreResource();
	    $read = $res->getConnection('core_read');
	    $select = $read->select()
             ->from($res->getTableName('sales/order'), array('entity_id'))
             ->where('parent_id=?', $oid)
             ->orWhere('entity_id=?', $oid);
echo $select;
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

    /*
    entity_type_id IN (order_status_history, invoice_comment, shipment_comment, creditmemo_comment)
    entity_id IN(order_id, credimemo_ids, invoice_ids, shipment_ids)
    attribute_id IN(order_status/comment_text, ....)
    */
	public function getAllCommentCollection($oid)
	{
	    $entityTypeIds = $this->getAllEntityTypeIds();
	    foreach($entityTypeIds as $eid=>$result){
            $etIds[] = $eid;
            $attributeIds[] = $result['comment_attribute_id'];
            $attributeIds[] = $result['notified_attribute_id'];
	    }
	    $res = $this->getCoreResource();
	    $read = $res->getConnection('core_read');
	    $select = $read->select()
             ->from(array('order' => $res->getTableName('sales/order')), array('entity_id'))
             ->join(array('comment'=>$res->getTableName('sales/order_entity_text')), "comment.entity_id=order.entity_id", array('postcode'=>'tax_postcode'))
             ->where('order.entity_id in (?)', implode(",", $this->getAllEntityIds($oid)))
             ->where('order.entity_type_id in (?)', implode(",", $etIds))
             //->where('attribute_set_id in (?)', implode(",", $attributeIds));
        ;
echo "<hr>";
echo $select;



	}


}