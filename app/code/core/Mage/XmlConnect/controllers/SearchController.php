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
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * XmlConnect search controller
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */

require_once 'Mage/CatalogSearch/controllers/ResultController.php';

class Mage_XmlConnect_SearchController extends Mage_CatalogSearch_ResultController
{
    /**
     * Declare content type header
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $this->getResponse()->setHeader('Content-type', 'text/xml; charset=UTF-8');
    }

    /**
     * Perform search products
     *
     */
    public function indexAction()
    {
        //cat	13
        //color	24
        //dir	asc
        //enable_googlecheckout	0
        //limit	9
        //manufacturer	111
        //order	name
        //price	1,1000
        //q	Intel
//        http://localhost:8888/magento/xmlconnect/index/category/category_id/28/offset/0/count/20/filter_price/3,1000/filter_color/23/order_price/desc

//        parent::indexAction();
        $xml = '<?xml version="1.0"?>
                <search>
                    <filters>
                        <item><name>Price</name><code>price</code><values><value>
                                <id>1,100</id>
                                <label>$0.00 - $100.00</label>
                                <count>1</count>
                          </value><value>
                                <id>2,100</id>
                                <label>$100.00 - $200.00</label>
                                <count>2</count>
                          </value><value>
                                <id>4,100</id>
                                <label>$300.00 - $400.00</label>
                                <count>1</count>
                          </value><value>
                                <id>6,100</id>
                                <label>$500.00 - $600.00</label>
                                <count>1</count>
                          </value></values></item><item><name>Color</name><code>color</code><values><value>
                                <id>24</id>
                                <label>Black</label>
                                <count>1</count>
                          </value><value>
                                <id>23</id>
                                <label>Silver</label>
                                <count>4</count>
                          </value></values></item><item><name>Megapixels</name><code>megapixels</code><values><value>
                                <id>93</id>
                                <label>5</label>
                                <count>2</count>
                          </value><value>
                                <id>91</id>
                                <label>7</label>
                                <count>1</count>
                          </value><value>
                                <id>90</id>
                                <label>8</label>
                                <count>2</count>
                          </value></values></item>
                    </filters>
                    <orders>
                         <item><code>position</code><name>Position</name></item><item><code>name</code><name>Name</name></item><item><code>price</code><name>Price</name></item>
                    </orders>
                    <products>
                        <item>
                            <entity_id>44</entity_id>
                            <name>Canon Digital Rebel XT 8MP Digital SLR Camera</name>
                            <in_stock>1</in_stock>
                            <rating_summary>8</rating_summary>
                            <reviews_count>4</reviews_count>
                            <icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/80x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</icon>
                            <big_icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/130x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</big_icon>
                            <price>$550.00</price>
                            <aslowas_price>As low as: $449.00</aslowas_price>
                            <stock_item>Array</stock_item>
                        </item>
                        <item>
                            <entity_id>45</entity_id>
                            <name> Argus QC-2185 Quick Click 5MP Digital Camera</name>
                            <in_stock>1</in_stock>
                            <rating_summary>7</rating_summary>
                            <reviews_count>1</reviews_count>
                            <icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/80x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</icon>
                            <big_icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/130x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</big_icon>
                            <price>$37.49</price>
                            <stock_item>Array</stock_item>
                        </item>
                        <item>
                            <entity_id>46</entity_id>
                            <name> Olympus Stylus 750 7.1MP Digital Camera</name>
                            <in_stock>1</in_stock>
                            <rating_summary>8</rating_summary>
                            <reviews_count>1</reviews_count>
                            <icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/80x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</icon>
                            <big_icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/130x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</big_icon>
                            <price>$161.94</price>
                            <stock_item>Array</stock_item>
                        </item>
                        <item>
                            <entity_id>47</entity_id>
                            <name>Canon PowerShot A630 8MP Digital Camera with 4x Optical Zoom</name>
                            <in_stock>1</in_stock>
                            <rating_summary>9</rating_summary>
                            <reviews_count>1</reviews_count>
                            <icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/80x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</icon>
                            <big_icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/130x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</big_icon>
                            <price>$329.99</price>
                            <stock_item>Array</stock_item>
                        </item>
                        <item>
                        <entity_id>48</entity_id>
                            <name>Kodak EasyShare C530 5MP Digital Camera</name>
                            <in_stock>1</in_stock>
                            <rating_summary>6</rating_summary>
                            <reviews_count>1</reviews_count>
                            <icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/80x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</icon>
                            <big_icon>http://localhost:8888/magento/media/catalog/product/cache/1/image/130x/9df78eab33525d08d6e5fb8d27136e95/images/catalog/product/placeholder/image.jpg</big_icon>
                            <price>$199.99</price>
                            <stock_item>Array</stock_item>
                        </item>
                    </products>
                </search>';
        $this->getResponse()->setBody($xml);
    }

    /**
     * Block redirect processing
     *
     * @param   string $defaultUrl
     * @return  Mage_XmlConnect_SearchController
     */
    protected function _redirectReferer($defaultUrl = null)
    {
        return $this;
    }

    /**
     * Load and render layouts
     *
     */
    protected function _initLayout()
    {
        $this->loadLayout(false);

        /**
         * Mage_Core_Model_Message_Collection
         */
//        $messages = Mage::getSingleton('catalog/session')->getMessages(true);
//        $messages = Mage::getSingleton('checkout/session')->getMessages(true);

        $this->renderLayout();
    }

}