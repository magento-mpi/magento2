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
 * @package     Mage_CatalogInventory
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * API2 Stock Item Persist Validator
 *
 * @category   Mage
 * @package    Mage_CatalogInventory
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Catalog_Model_Api2_Product_Website_Validator_Website extends Mage_Api2_Model_Resource_Validator
{
    /**
     * Validate data.
     * If fails validation, then this method returns false, and
     * getErrors() will return an array of errors that explain why the
     * validation failed.
     *
     * @param array $data
     * @void bool
     */
    public function isSatisfiedByData(array $data)
    {
        if (!isset($data['website_id']) || !is_numeric($data['website_id'])) {
            $this->_addError('Invalid value for "website_id" in request.');
        }

        if (isset($data['copy_to_stores'])) {
            foreach ($data['copy_to_stores'] as $storeData) {
                if (!isset($storeData['store_from']) || !is_numeric($storeData['store_from'])) {
                    $this->_addError(sprintf('Invalid value for "store_from" for the website with ID в„–1234.',
                        $data['website_id']));
                } else {
                    $storeFrom = Mage::getModel('core/store')->load($storeData['store_from']);
                    if (!$storeFrom->getId()) {
                        $this->_addError(sprintf('Store not found #%s for website_id #%s.', $storeData['store_from'],
                            $data['website_id']));
                    }
                }
                if (!isset($storeData['store_to']) || !is_numeric($storeData['store_to'])) {
                    $this->_addError(sprintf('Invalid value for "store_to" in the request for website_id #%s.',
                        $data['website_id']));
                } else {
                    $storeTo = Mage::getModel('core/store')->load($storeData['store_to']);
                    if (!$storeTo->getId()) {
                        $this->_addError(sprintf('Store not found #%s for website_id #%s.', $storeData['store_to'],
                            $data['website_id']));
                    }
                }
            }
        }

        return !count($this->getErrors());
    }
}
