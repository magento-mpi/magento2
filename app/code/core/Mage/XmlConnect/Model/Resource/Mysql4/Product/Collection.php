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
 * @package     Mage_Catalog
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Product resource collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
    extends Mage_Catalog_Model_Resource_Eav_Mysql4_Product_Collection
{

    protected $_itemObjectClass = 'Mage_Xmlconnect_Model_Product';

    protected function _beforeLoad()
    {
        $this->joinField('rating_summary',
                         'review_entity_summary',
                         'rating_summary',
                         'entity_pk_value=entity_id',
                         array('entity_type'=>1, 'store_id'=> Mage::app()->getStore()->getId()),
                         'left')
             ->joinField('reviews_count',
                         'review_entity_summary',
                         'reviews_count',
                         'entity_pk_value=entity_id',
                         array('entity_type'=>1, 'store_id'=> Mage::app()->getStore()->getId()),
                         'left')
             ->addAttributeToSelect(array('image'));

        return parent::_beforeLoad();
    }

    /**
     * @param int $offset
     * @param int $count
     * @return Mage_XmlConnect_Model_Resource_Mysql4_Product_Collection
     */
    public function addLimit($offset, $count)
    {
        $this->getSelect()->limit($count, $offset);
        return $this;
    }
    
}