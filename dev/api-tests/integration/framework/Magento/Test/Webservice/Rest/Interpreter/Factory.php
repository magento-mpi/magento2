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
 * REST content type interpreter factory
 *
 * @category    Magento
 * @package     Magento_Test
 * @author      Magento Api Team <api-team@magento.com>
 */
class Magento_Test_Webservice_Rest_Interpreter_Factory
{
    /**
     * Default interpreter content type
     */
    const DEFAULT_CONTENT_TYPE = 'text/plain';

    /**
     * Get interpreter object
     *
     * @param string $contentType
     * @return Magento_Test_Webservice_Rest_Interpreter_Interface
     */
    public static function getInterpreter($contentType)
    {
        $interpreters = array(
            'application/xml'  => 'Magento_Test_Webservice_Rest_Interpreter_Xml',
            'application/json' => 'Magento_Test_Webservice_Rest_Interpreter_Json',
            'text/json'        => 'Magento_Test_Webservice_Rest_Interpreter_Json',
            'text/plain'       => 'Magento_Test_Webservice_Rest_Interpreter_Query',
        );

        if (!array_key_exists($contentType, $interpreters)) {
            $contentType = self::DEFAULT_CONTENT_TYPE;
        }

        $interpreterClass = $interpreters[$contentType];
        return new $interpreterClass();
    }
}
