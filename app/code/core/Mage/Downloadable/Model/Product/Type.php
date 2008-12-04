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

    /**
     * Get downloadable product links
     *
     * @return array
     */
    public function getLinks()
    {
        return array(
            0 => new Varien_Object(array(
                'title' => 'Link1',
                'sample_url'   => Mage::getUrl('*/*/*', array('link'=>1))
            )),
            1 => new Varien_Object(array(
                'title' => 'Link2',
                'sample_url'   => Mage::getUrl('*/*/*', array('link'=>2))
            )),
            2 => new Varien_Object(array(
                'title' => 'Link3',
                'sample_url'   => Mage::getUrl('*/*/*', array('link'=>3))
            )),
            3 => new Varien_Object(array(
                'title' => 'Link4',
                'sample_url'   => Mage::getUrl('*/*/*', array('link'=>4))
            ))
        );
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
        return $this->getProduct()->getLinksPurchasedSeparately() || parent::hasOptions();
    }

    /**
     * Check if product cannot be purchased with no links selected
     *
     * @return boolean
     */
    public function getLinkSelectionRequired()
    {
        return $this->getProduct()->getLinksPurchasedSeparately() && (0 == $this->getProduct()->getPrice());
    }

    /**
     * Get downloadable product samples
     *
     * @return array
     */
    public function getSamples()
    {
        return array(
            '1' => new Varien_Object(array(
                'label' => 'Sample1',
                'url'   => Mage::getUrl('*/*/*', array('sample'=>1))
            )),
            '2' => new Varien_Object(array(
                'label' => 'Sample2',
                'url'   => Mage::getUrl('*/*/*', array('sample'=>2))
            )),
            '3' => new Varien_Object(array(
                'label' => 'Sample3',
                'url'   => Mage::getUrl('*/*/*', array('sample'=>3))
            )),
            '4' => new Varien_Object(array(
                'label' => 'Sample4',
                'url'   => Mage::getUrl('*/*/*', array('sample'=>4))
            ))
        );
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

}