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
 * Abstract Api2 model for review item
 *
 * @category   Mage
 * @package    Mage_Review
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Review_Model_Api2_Review_Rest extends Mage_Api2_Model_Resource_Instance
{
    /**
     * Resource name
     */
    const RESOURCE_NAME = 'review';

    /**
     * Helper for review specific data validation
     *
     * @var Mage_Review_Model_Api2_Validator
     */
    protected $_validator;

    /**
     * Initialize validator
     */
    function __construct()
    {
        $this->_validator = Mage::getModel('review/api2_validator');
    }

    /**
     * Fetch resource type
     *
     * @return string
     */
    public function getType()
    {
        return self::RESOURCE_NAME;
    }

    /**
     * Retrieve information about specified review item
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        $review = $this->_loadReview();
        return $review->getData();
    }

    /**
     * Load review by its id passed through request
     *
     * @throws Mage_Api2_Exception
     * @return Mage_Review_Model_Review
     */
    abstract protected function _loadReview();

    /**
     * Available attributes
     *
     * @return array
     * @todo Investigate list of attributes and add it to this method
     */
    static public function getAvailableAttributes()
    {
        return array(
             'entity_id' => 'ID',
             'content' => 'Content',
             'created_at' => 'Created At',
        );
    }
}
