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
 * @package     Mage_Review
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for associated products collection
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Products_Grouped_Rest_Admin_V1
    extends Mage_Catalog_Model_Api2_Products_Grouped_Rest
{
    protected function _create(array $data)
    {
        $required = array('simple_product_id');
        $notEmpty = array('simple_product_id', 'qty', 'position');
        $this->_validate($data, $required, $notEmpty);

        $simpleProductId = $data['simple_product_id'];
        /** @var $simpleProduct Mage_Catalog_Model_Product */
        $simpleProduct = Mage::getModel('catalog/product')->load($simpleProductId);
        if (!$simpleProduct->getId()) {
            $this->_critical('Simple product not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if ($simpleProduct->getTypeId() != Mage_Catalog_Model_Product_Type::TYPE_SIMPLE) {
            $this->_critical(sprintf('Product #%d type is not simple', $simpleProductId),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $groupedProductId = $this->getRequest()->getParam('id');
        /** @var $groupedProduct Mage_Catalog_Model_Product */
        $groupedProduct = Mage::getModel('catalog/product')->load($groupedProductId);
        if (!$groupedProduct->getId()) {
            $this->_critical('Super product not found', Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }
        if (!$groupedProduct->isGrouped()) {
            $this->_critical(sprintf('Product #%d type is not grouped', $groupedProductId),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $groupedLinks = array();
        /** @var $link Mage_Catalog_Model_Product_Link */
        foreach ($groupedProduct->getGroupedLinkCollection() as $link) {
            $groupedLinks[$link->getLinkedProductId()] = array(
                'qty' => $link->getQty(),
                'position' => $link->getPosition()
            );
        }
        if (array_key_exists($simpleProductId, $groupedLinks)) {
            $this->_critical(sprintf('Product #%d is already linked as grouped to product #%d',
                $simpleProductId, $groupedProductId), Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        }

        $groupedLinks[$simpleProductId] = array(
            'qty' => isset($data['qty']) ? $data['qty'] : 0,
            'position' => isset($data['position']) ? $data['position'] : 0,
        );
        $groupedProduct->setGroupedLinkData($groupedLinks);
        try{
            $groupedProduct->save();
        } catch (Mage_Core_Exception $e) {
            $this->_error($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }

        return true;
    }
}
