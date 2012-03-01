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
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 for products instance
 *
 * @category   Mage
 * @package    Mage_Catalog
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Rest_Admin_V1 extends Mage_Catalog_Model_Api2_Product_Rest
{
    /**
     * @var Mage_Catalog_Model_Api2_Helper
     */
    protected $_productResourceHelper;

    /**
     * Initialize product resource helper
     */
    function __construct()
    {
        $this->_productResourceHelper = Mage::getSingleton('catalog/api2_helper');
    }

    /**
     * Retrieve product data
     *
     * @return array
     */
    protected function _retrieve()
    {
        $product = $this->_getProduct();
        return $product->getData();
    }

    /**
     * Delete product by its ID
     *
     * @throws Mage_Api2_Exception
     */
    protected function _delete()
    {
        $product = $this->_getProduct();
        try {
            $product->delete();
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_INTERNAL_ERROR);
        }
    }

    /**
     * Update product by its ID
     *
     * @param array $data
     */
    protected function _update(array $data)
    {
        $this->_validate($data);
        /** @var $product Mage_Catalog_Model_Product */
        $product = $this->_getProduct();
        if (isset($data['sku'])) {
            $product->setSku($data['sku']);
        }
        $this->_productResourceHelper->prepareDataForSave($product, $data);
        try {
            $product->validate();
            $product->save();
        } catch (Mage_Eav_Model_Entity_Attribute_Exception $e) {
            $this->_critical(sprintf('Invalid attribute "%s": %s', $e->getAttributeCode(), $e->getMessage()),
                Mage_Api2_Model_Server::HTTP_BAD_REQUEST);
        } catch (Mage_Core_Exception $e) {
            $this->_critical($e->getMessage(), Mage_Api2_Model_Server::HTTP_INTERNAL_ERROR);
        } catch (Exception $e) {
            $this->_critical(self::RESOURCE_UNKNOWN_ERROR);
        }
    }

    /**
     * Pre-validate request data
     *
     * @param array $data
     * @param array $required
     * @param array $notEmpty
     */
    protected function _validate(array $data, array $required = array(), array $notEmpty = array())
    {
        parent::_validate($data, $required, $notEmpty);
        $validationErrors = $this->_productResourceHelper->validateProductData($data, $this->_getProduct());
        foreach ($validationErrors as $error) {
            $this->_error($error['message'], $error['code']);
        }
        if ($this->getResponse()->isException()) {
            $this->_critical(self::RESOURCE_DATA_PRE_VALIDATION_ERROR);
        }
    }
}
