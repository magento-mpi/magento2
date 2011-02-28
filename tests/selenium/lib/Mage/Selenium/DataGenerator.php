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
 * @category    tests
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @author      Magento Core Team <core@magentocommerce.com>
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Data generator class
 *
 * @package     selenium
 * @subpackage  Mage_Selenium
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class Mage_Selenium_DataGenerator
{

    public function generate($type='string', $length=100, $modifier=null, $prefix=null)
    {
        switch ($type) {
            case 'email':
                // @TODO
            case 'text':
                // @TODO
            case 'string':
                $result = $this->generateRandomString($length, $modifier);
                break;
            default:
                // @TODO trow error
                break;
        }
        
        return $result;
    }

    /**
     * Generates email address
     *
     * @param int $length todo
     * @param string $validity todo
     * @param string $prefix todo
     * @return string
     */
    public function generateEmailAddress($length=100, $validity='valid', $prefix='')
    {
        $result = '';
        // @TODO

        return $result;
    }

    /**
     * Generates random string
     *
     * @param type $length
     * @param type $class
     * @return string
     */
    public function generateRandomString($length=100, $class=':alnum:')
    {
        $result = '';
        // @TODO

        return $result;
    }

}
