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
 * to license@magento.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magento.com for more information.
 *
 * @category    Magento
 * @package     Magento
 * @subpackage  integration_tests
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magento.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * REST XML decoder
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Magento_Test_Webservice_Rest_Interpreter_Xml
    implements Magento_Test_Webservice_Rest_Interpreter_Interface
{
    /**
     * Decode XML
     *
     * @param string $input raw xml data
     * @return array
     */
    public function decode($input)
    {
        $xml = new Varien_Simplexml_Element($input);

        return $xml->asCanonicalArray();
    }

    /**
     * Encode input array to XML
     *
     * @param array $input
     * @return string
     */
    public function encode($input)
    {
        $writer = new Zend_Config_Writer_Xml();
        $config = new Zend_Config($input);
        $writer->setConfig($config);
        $xml = $writer->render();
        return $xml;
    }
}
