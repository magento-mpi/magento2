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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Downloadable product type model
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Model_Product_Type extends Mage_Catalog_Model_Product_Type_Virtual
{

    const TYPE_DOWNLOADABLE = 'downloadable';

    protected $_sampleCollection = null;
    protected $_linkCollection   = null;

    /**
     * Get downloadable product links
     *
     * @return array
     */
    public function getLinks()
    {
        $product = $this->getProduct();
        if (is_null($product->getDownloadableLinks())) {
            $_linkCollection = Mage::getModel('downloadable/link')->getCollection()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId())
                ->addPriceToResult($product->getStore()->getWebsiteId());
            $linksCollectionById = array();
            foreach ($_linkCollection as $link) {
                $link->setProduct($product);
                $linksCollectionById[$link->getId()] = $link;
            }
            $product->setDownloadableLinks($linksCollectionById);
        }
        return $product->getDownloadableLinks();
    }

    /**
     * Check if product has links
     *
     * @return boolean
     */
    public function hasLinks()
    {
        return count($this->getLinks()) > 0;
    }

    /**
     * Check if product has options
     *
     * @return boolean
     */
    public function hasOptions()
    {
        return true;
        return $this->getProduct()->getLinksPurchasedSeparately() || parent::hasOptions();
    }

    /**
     * Check if product cannot be purchased with no links selected
     *
     * @return boolean
     */
    public function getLinkSelectionRequired()
    {
        return true;
        return $this->getProduct()->getLinksPurchasedSeparately() && (0 == $this->getProduct()->getPrice());
    }

    /**
     * Get downloadable product samples
     *
     * @return array
     */
    public function getSamples()
    {
        if (is_null($this->_sampleCollection)) {
            $product = $this->getProduct();
            $this->_sampleCollection = Mage::getModel('downloadable/sample')->getCollection()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId());
        }

        return $this->_sampleCollection;
    }

    /**
     * Check if product has samples
     *
     * @return boolean
     */
    public function hasSamples()
    {
        return count($this->getSamples()) > 0;
    }

    public function save()
    {
        parent::save();

        $product = $this->getProduct();

        if ($data = $product->getDownloadableData()) {

            foreach ($data['sample'] as $sampleItem) {
                $sampleModel = Mage::getModel('downloadable/sample')
                    ->setData($sampleItem)
                    ->setProductId($product->getId())
                    ->setStoreId($product->getStoreId());
                $sampleModel->save();
            }

            foreach ($data['link'] as $linkItem) {
                $linkModel = Mage::getModel('downloadable/link')
                    ->setData($linkItem)
                    ->setProductId($product->getId())
                    ->setStoreId($product->getStoreId())
                    ->setWebsiteId($product->getStore()->getWebsiteId());
                $linkModel->save();
            }
        }

        return $this;
    }

    public function prepareForCart(Varien_Object $buyRequest)
    {
        $result = parent::prepareForCart($buyRequest);

        if (is_string($result)) {
            return $result;
        }
        $preparedLinks = array();
        if ($links = $buyRequest->getLinks()) {
            foreach ($this->getLinks() as $link) {
                if (in_array($link->getId(), $links)) {
                    $preparedLinks[] = $link->getId();
                }
            }
        }
        if ($preparedLinks) {
            $this->getProduct()->addCustomOption('downloadable_link_ids', implode(',', $preparedLinks));
            return $result;
        }
        if ($this->getLinkSelectionRequired()) {
            return Mage::helper('downloadable')->__('Please specify product link(s).');
        }
        return $result;
    }

}