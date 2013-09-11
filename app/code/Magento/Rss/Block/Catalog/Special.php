<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Review form block
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rss\Block\Catalog;

class Special extends \Magento\Rss\Block\Catalog\AbstractCatalog
{
    /**
     * \Zend_Date object for date comparsions
     *
     * @var \Zend_Date $_currentDate
     */
    protected static $_currentDate = null;

    protected function _construct()
    {
        /*
        * setting cache to save the rss for 10 minutes
        */
        $this->setCacheKey('rss_catalog_special_'.$this->_getStoreId().'_'.$this->_getCustomerGroupId());
        $this->setCacheLifetime(600);
    }

    protected function _toHtml()
    {
         //store id is store view id
        $storeId = $this->_getStoreId();
        $websiteId = \Mage::app()->getStore($storeId)->getWebsiteId();

        //customer group id
        $customerGroupId = $this->_getCustomerGroupId();

        $product = \Mage::getModel('\Magento\Catalog\Model\Product');

        $fields = array(
            'final_price',
            'price'
        );
        $specials = $product->setStoreId($storeId)->getResourceCollection()
            ->addPriceDataFieldFilter('%s < %s', $fields)
            ->addPriceData($customerGroupId, $websiteId)
            ->addAttributeToSelect(
                    array(
                        'name', 'short_description', 'description', 'price', 'thumbnail',
                        'special_price', 'special_to_date',
                        'msrp_enabled', 'msrp_display_actual_price_type', 'msrp'
                    ),
                    'left'
            )
            ->addAttributeToSort('name', 'asc')
        ;

        $newurl = \Mage::getUrl('rss/catalog/special/store_id/' . $storeId);
        $title = __('%1 - Special Products', \Mage::app()->getStore()->getFrontendName());
        $lang = \Mage::getStoreConfig('general/locale/code');

        $rssObj = \Mage::getModel('\Magento\Rss\Model\Rss');
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
        \Mage::getSingleton('Magento\Core\Model\Resource\Iterator')->walk(
            $specials->getSelect(),
            array(array($this, 'addSpecialXmlCallback')),
            array('rssObj'=> $rssObj, 'results'=> &$results)
        );

        if (sizeof($results)>0) {
            foreach($results as $result){
                // render a row for RSS feed
                $product->setData($result);
                $html = sprintf('<table><tr>
                    <td><a href="%s"><img src="%s" alt="" border="0" align="left" height="75" width="75" /></a></td>
                    <td style="text-decoration:none;">%s',
                    $product->getProductUrl(),
                    $this->helper('\Magento\Catalog\Helper\Image')->init($product, 'thumbnail')->resize(75, 75),
                    $this->helper('\Magento\Catalog\Helper\Output')->productAttribute(
                        $product,
                        $product->getDescription(),
                        'description'
                    )
                );

                // add price data if needed
                if ($product->getAllowedPriceInRss()) {
                    if (\Mage::helper('Magento\Catalog\Helper\Data')->canApplyMsrp($product)) {
                        $html .= '<br/><a href="' . $product->getProductUrl() . '">'
                            . __('Click for price') . '</a>';
                    } else {
                        $special = '';
                        if ($result['use_special']) {
                            $special = '<br />' . __('Special Expires On: %1', $this->formatDate($result['special_to_date'], \Magento\Core\Model\LocaleInterface::FORMAT_TYPE_MEDIUM));
                        }
                        $html .= sprintf('<p>%s %s%s</p>',
                            __('Price: %1', \Mage::helper('Magento\Core\Helper\Data')->currency($result['price'])),
                            __('Special Price: %1', \Mage::helper('Magento\Core\Helper\Data')->currency($result['final_price'])),
                            $special
                        );
                    }
                }

                $html .= '</td></tr></table>';

                $rssObj->_addEntry(array(
                    'title'       => $product->getName(),
                    'link'        => $product->getProductUrl(),
                    'description' => $html
                ));
            }
        }
        return $rssObj->createRssXml();
    }

    /**
     * Preparing data and adding to rss object
     *
     * @param array $args
     */
    public function addSpecialXmlCallback($args)
    {
        if (!isset(self::$_currentDate)) {
            self::$_currentDate = new \Zend_Date();
        }

        // dispatch event to determine whether the product will eventually get to the result
        $product = new \Magento\Object(array('allowed_in_rss' => true, 'allowed_price_in_rss' => true));
        $args['product'] = $product;
        \Mage::dispatchEvent('rss_catalog_special_xml_callback', $args);
        if (!$product->getAllowedInRss()) {
            return;
        }

        // add row to result and determine whether special price is active (less or equal to the final price)
        $row = $args['row'];
        $row['use_special'] = false;
        $row['allowed_price_in_rss'] = $product->getAllowedPriceInRss();
        if (isset($row['special_to_date']) && $row['final_price'] <= $row['special_price']
            && $row['allowed_price_in_rss']
        ) {
            $compareDate = self::$_currentDate->compareDate($row['special_to_date'], \Magento\Date::DATE_INTERNAL_FORMAT);
            if (-1 === $compareDate || 0 === $compareDate) {
                $row['use_special'] = true;
            }
        }

       $args['results'][] = $row;
    }


    /**
     * Function for comparing two items in collection
     *
     * @param   \Magento\Object $item1
     * @param   \Magento\Object $item2
     * @return  boolean
     */
    public function sortByStartDate($a, $b)
    {
        return $a['start_date']>$b['start_date'] ? -1 : ($a['start_date']<$b['start_date'] ? 1 : 0);
    }
}
