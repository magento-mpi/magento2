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
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Base class for all API collection resources
 */
abstract class Mage_Api2_Model_Resource_Instance extends Mage_Api2_Model_Resource
{
    /**
     * Internal "instance" resource model dispatch
     */
    public function dispatch()
    {
        $operation = $this->getRequest()->getOperation();

        switch ($operation) {
            //not exist for this kind of resource
            case self::OPERATION_CREATE:
                $this->_create(array());
                break;

            case self::OPERATION_UPDATE:
                $data = $this->getRequest()->getBodyParams();
                $filtered = $this->getFilter()->in($data);
                $this->$operation($filtered);
                break;

            case self::OPERATION_RETRIEVE:
                //TODO how we process &include, what attributes we show by default, all allowed, all static?
                $result = $this->_retrieve();
                $filtered = $this->getFilter()->out($result);
                $this->_render($filtered);
                break;

            case self::OPERATION_DELETE:
                $this->_delete();
                break;
        }
    }

    /**
     * Create method not allowed for this type of resource
     *
     * @param array $data
     */
    final protected function _create(array $data)
    {
        $this->_critical(self::RESOURCE_METHOD_NOT_ALLOWED);
    }
}
