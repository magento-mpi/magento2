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
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order creditmemo configuration model
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo_Config extends Mage_Sales_Model_Order_Total_Config_Base
{
    /**
     * Credit memo total modles list
     *
     * @var null
     */
    protected $_totalModels = null;

    /**
     * Cache key for collectors
     *
     * @var string
     */
    protected $_collectorsCacheKey = 'sorted_order_creditmemo_collectors';

    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct(Mage::getConfig()->getNode('global/sales/order_creditmemo'));
        $this->_initModels();
        $this->_initCollectors();
    }

    /**
     * Retrieve invoice total calculation models
     *
     * @return array
     */
    public function getTotalModels()
    {
        if (is_null($this->_totalModels)) {
            foreach ($this->_collectors as $totalConfig) {
                $class = $totalConfig->getTotalConfigNode()->getClassName();
                if ($class && ($model = Mage::getModel($class))) {
                    $this->_totalModels[] = $model;
                }
            }
        }
        return $this->_totalModels;
    }
}
