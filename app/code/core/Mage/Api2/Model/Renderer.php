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
 * Webservice apia2 renderer model
 *
 * @category   Mage
 * @package    Mage_Api2
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Api2_Model_Renderer
{
    /**
     * Get Renderer of given type
     *
     * @static
     * @param mixed $input
     * @throws Mage_Api2_Exception
     * @return Mage_Api2_Model_Renderer_Interface
     */
    public static function factory($input=null)
    {
        if (is_string($input)) {
            $renderType = $input;
        } elseif ($input instanceof Mage_Api2_Model_Request) {
            $request = $input;

            /** @var $helper Mage_Api2_Helper_Data */
            $helper = Mage::helper('api2');

            $renderType = $helper->getRendererType($request);    //this can also throw Exception with code 406 for example
        } else {
            throw new Exception('');
        }

        return Mage::getModel($renderType);
    }
}
