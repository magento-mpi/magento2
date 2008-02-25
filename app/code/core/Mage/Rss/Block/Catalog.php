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
 * Review form block
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author     Lindy Kyaw <lindy@varien.com>
 */
class Mage_Rss_Block_Catalog extends Mage_Core_Block_Template
{
    protected function _toHtml()
    {
        $type = $this->getType() ? $this->getType() : 'new';
        if ($type=='special') {
            return $this->specialProductHtml();
        } elseif ($type=='salesrule') {
            return $this->salesruleProductHtml();
        } elseif ($type=='tag') {
            return $this->taggedProductHtml();
        } else {
            return $this->newProductHtml();
        }
        //return $this->getType();
    }

    protected function newProductHtml()
    {
        //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('sid');
        if($storeId == null) {
           $storeId = Mage::app()->getStore()->getId();
        }

        $newurl = Mage::getUrl('rss/catalog/new');
        $title = Mage::helper('rss')->__('%s - New Products',Mage::app()->getStore()->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);
/*
oringinal price - getPrice() - inputed in admin
special price - getSpecialPrice()
getFinalPrice() - used in shopping cart calculations
*/

        $product = Mage::getModel('catalog/product');
        $todayDate = $product->getResource()->formatDate(time());

        $products = $product->setStoreId($storeId)->getCollection()
            ->addAttributeToFilter('news_from_date', array('date'=>true, 'to'=> $todayDate))
            ->addAttributeToFilter(array(array('attribute'=>'news_to_date', 'date'=>true, 'from'=>$todayDate), array('attribute'=>'news_to_date', 'is' => new Zend_Db_Expr('null'))),'','left')
            ->addAttributeToSort('news_from_date','desc')
            ->addAttributeToSelect(array('name', 'short_description', 'description', 'price', 'thumbnail'), 'inner')
            ->addAttributeToSelect(array('special_price', 'special_from_date', 'special_to_date'), 'left')
        ;
        Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);
        Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);

        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')
            ->walk($products->getSelect(), array(array($this, 'addNewItemXmlCallback')), array('rssObj'=> $rssObj, 'product'=>$product));

        return $rssObj->createRssXml();
    }

    public function addNewItemXmlCallback($args)
    {
        $product = $args['product'];
        $product->setData($args['row']);
        $description = '<table><tr>'.
            '<td><a href="'.$product->getProductUrl().'"><img src="'.$product->getThumbnailUrl().'" border="0" align="left" height="75" width="75"></a></td>'.
            '<td  style="text-decoration:none;">'.$product->getDescription().
            '<p> Price:'.Mage::helper('core')->currency($product->getPrice()).
            ($product->getPrice() != $product->getFinalPrice() ? ' Special Price:'. Mage::helper('core')->currency($product->getFinalPrice()) : '').
            '</p>'.
            '</td>'.
            '</tr></table>';
        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,

                );
        $rssObj->_addEntry($data);
    }

    protected function specialProductHtml()
    {
         //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('sid');
        if($storeId == null) {
           $storeId = Mage::app()->getStore()->getId();
        }
$storeId = 1;
        //customer group id
        $custGroup =   (int) $this->getRequest()->getParam('cid');
        if($custGroup == null) {
            $custGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }

        $product = Mage::getModel('catalog/product');
        $todayDate = $product->getResource()->formatDate(time());

        $rulePriceWhere = "({{table}}.rule_date is null) or ({{table}}.rule_date='$todayDate' and {{table}}.store_id='$storeId' and {{table}}.customer_group_id='$custGroup')";

        $specials = $product->setStoreId($storeId)->getResourceCollection()
            ->addAttributeToFilter('special_price', array('gt'=>0), 'left')
            ->addAttributeToFilter('special_from_date', array('date'=>true, 'to'=> $todayDate), 'left')
            ->addAttributeToFilter(array(
                array('attribute'=>'special_to_date', 'date'=>true, 'from'=>$todayDate),
                array('attribute'=>'special_to_date', 'is' => new Zend_Db_Expr('null'))
            ), '', 'left')
            ->addAttributeToSort('special_from_date', 'desc')
            ->addAttributeToSelect(array('name', 'short_description', 'description', 'price', 'thumbnail'), 'inner')
            ->joinTable('catalogrule/rule_product_price', 'product_id=entity_id', array('rule_price'=>'rule_price', 'rule_start_date'=>'latest_start_date'), $rulePriceWhere, 'left')
        ;

        $rulePriceCollection = Mage::getResourceModel('catalogrule/rule_product_price_collection')
            ->addFieldToFilter('store_id', $storeId)
            ->addFieldToFilter('customer_group_id', $custGroup)
            ->addFieldToFilter('rule_date', $todayDate);

        $productIds = $rulePriceCollection->getProductIds();

        if (!empty($productIds)) {
            $specials->getSelect()->orWhere('e.entity_id in ('.implode(',',$productIds).')');
        }

//echo $specials->getSelect();

        $newurl = Mage::getUrl('rss/catalog/new');
        $title = Mage::helper('rss')->__('%s - Special Discounts', Mage::app()->getStore()->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);

        $results = array();
        /*
        using resource iterator to load the data one by one
        instead of loading all at the same time. loading all data at the same time can cause the big memory allocation.
        */
        Mage::getSingleton('core/resource_iterator')
            ->walk($specials->getSelect(), array(array($this, 'addSpecialXmlCallback')), array('rssObj'=> $rssObj, 'results'=> &$results));

        if(sizeof($results)>0){
           usort($results, array(&$this, 'sortByStartDate'));

           foreach($results as $result){
               $product->setData($result);
//print_r($product->getData());
               $special_price = ($result['use_special'] ? $result['special_price'] : $result['rule_price']);
               $description = '<table><tr>'.
                '<td><a href="'.$product->getProductUrl().'"><img src="'.$product->getThumbnailUrl().'" border="0" align="left" height="75" width="75"></a></td>'.
                '<td  style="text-decoration:none;">'.$product->getId()."***".$product->getDescription().
                '<p> Price:'.Mage::helper('core')->currency($product->getPrice()).
                ' Special Price:'. Mage::helper('core')->currency($special_price).
                ($result['use_special'] && $result['special_to_date'] ? '<br/> Special Expired in: '.$this->formatDate($result['special_to_date'], Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM) : '').
                '</p>'.
                '</td>'.
                '</tr></table>';
                $data = array(
                        'title'         => $product->getName(),
                        'link'          => $product->getProductUrl(),
                        'description'   => $description,

                        );
                $rssObj->_addEntry($data);
           }
        }
        return $rssObj->createRssXml();
    }

    public function addSpecialXmlCallback($args)
    {
//echo "<pre>";
//print_r($args['row']);
       $row = $args['row'];
       $special_price = $row['special_price'];
       $rule_price = $row['rule_price'];
       if (!$rule_price || ($rule_price && $special_price && $special_price<=$rule_price)) {
           $row['start_date'] = $row['special_from_date'];
           $row['use_special'] = true;
       } else {
           $row['start_date'] = $row['rule_start_date'];
           $row['use_special'] = false;
       }
       $args['results'][] = $row;
    }

    /**
     * Function for comparing two items in collection
     *
     * @param   Varien_Object $item1
     * @param   Varien_Object $item2
     * @return  boolean
     */
    public function sortByStartDate($a, $b)
    {
        return $a['start_date']>$b['start_date'] ? -1 : ($a['start_date']<$b['start_date'] ? 1 : 0);
    }

    protected function salesruleProductHtml()
    {
        //store id is store view id
        $storeId =   (int) $this->getRequest()->getParam('sid');
        if($storeId == null) {
           $storeId = Mage::app()->getStore()->getId();
        }

        //customer group id
        $custGroup =   (int) $this->getRequest()->getParam('cid');
        if($custGroup == null) {
            $custGroup = Mage::getSingleton('customer/session')->getCustomerGroupId();
        }


        $newurl = Mage::getUrl('rss/catalog/new');
        $title = Mage::helper('rss')->__('%s - Discounts and Coupons',Mage::app()->getStore($storeId)->getName());
        $lang = Mage::getStoreConfig('general/locale/code');

        $rssObj = Mage::getModel('rss/rss');
        $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
                );
        $rssObj->_addHeader($data);

        $now = date('Y-m-d');
        $_saleRule = Mage::getModel('salesrule/rule');
        $collection = $_saleRule->getResourceCollection()
                    ->addFieldToFilter('from_date', array('date'=>true, 'to' => $now))
                    ->addFieldToFilter('store_ids',array('finset' => $storeId))
        			->addFieldToFilter('customer_group_ids', array('finset' => $custGroup))
        			->addFieldToFilter('is_rss',1)
        			->addFieldToFilter('is_active',1)
        			->setOrder('from_date','desc');
        $collection->getSelect()->where('to_date is null or to_date>=?', $now);
        $collection->load();

        $url = Mage::getUrl('');

        foreach ($collection as $sr) {
            $description = '<table><tr>'.
            '<td style="text-decoration:none;">'.$sr->getDescription().
            '<br/>Discount Start Date: '.$this->formatDate($sr->getFromDate(), 'medium').
            '<br/>Discount End Date: '.$this->formatDate($sr->getToDate(), 'medium').
            ($sr->getCouponCode() ? '<br/> Coupon Code: '.$sr->getCouponCode().'' : '').
            '</td>'.
            '</tr></table>';
             $data = array(
                'title'         => $sr->getName(),
                'description'   => $description,
                'link'          => $url
                );
            $rssObj->_addEntry($data);
        }
        return $rssObj->createRssXml();
    }


    protected function taggedProductHtml()
    {
         //store id is store view id
         $storeId =   (int) $this->getRequest()->getParam('sid');
         if ($storeId == null) {
            $storeId = Mage::app()->getStore()->getId();
         }
         $tagName = $this->getRequest()->getParam('tagName');

         $tagModel = Mage::getModel('tag/tag');
         $tagModel->loadByName($tagName);

         if ($tagModel->getId() && $tagModel->getStatus()==$tagModel->getApprovedStatus()) {
            $newurl = Mage::getUrl('rss/catalog/new');
            $title = Mage::helper('rss')->__('Products tagged with %s', $tagModel->getName());
            $lang = Mage::getStoreConfig('general/locale/code');

            $rssObj = Mage::getModel('rss/rss');
            $data = array('title' => $title,
                'description' => $title,
                'link'        => $newurl,
                'charset'     => 'UTF-8',
                'language'    => $lang
            );
            $rssObj->_addHeader($data);

            $_collection = $tagModel->getEntityCollection()
                ->addTagFilter($tagModel->getId())
                ->addStoreFilter($storeId);

            $product = Mage::getModel('catalog/product');

            Mage::getSingleton('core/resource_iterator')
                    ->walk($_collection->getSelect(), array(array($this, 'addTaggedItemXml')), array('rssObj'=> $rssObj, 'product'=>$product));

            return $rssObj->createRssXml();
         }
    }

    public function addTaggedItemXml($args)
    {
        $product = $args['product'];
        $product->unsetData()->load($args['row']['entity_id']);
        $description = '<table><tr>'.
        '<td><a href="'.$product->getProductUrl().'"><img src="'.$product->getThumbnailUrl().'" border="0" align="left" height="75" width="75"></a></td>'.
        '<td  style="text-decoration:none;">'.$product->getDescription().
        '<p> Price:'.Mage::helper('core')->currency($product->getFinalPrice()).'</p>'.
        '</td>'.
        '</tr></table>';
        $rssObj = $args['rssObj'];
        $data = array(
                'title'         => $product->getName(),
                'link'          => $product->getProductUrl(),
                'description'   => $description,

                );
        $rssObj->_addEntry($data);
    }
}
