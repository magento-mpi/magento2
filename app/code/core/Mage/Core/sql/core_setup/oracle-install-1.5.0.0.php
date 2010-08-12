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
 * @package     Mage_Core
 * @copyright   Copyright (c) 2010 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->getConnection()->query("
    create or replace and compile java source named STRING_FUNCTION as
    public class StringFuctions
    {
        public static void entry()
        {

        }
        public static int findInSet(String whatSearch, String whereSearch )
        {
            String separator = \",\";
            String[] searchValues = whereSearch.split(separator);
            for(int i = 0; i < searchValues.length ; i++) {
                if (whatSearch.compareTo(searchValues[i]) == 0) {
                    return 1;
                }
            }
            return 0;
        }
    }
");

$installer->getConnection()->query("
    CREATE OR REPLACE FUNCTION FIND_IN_SET(p_what_search IN VARCHAR2, p_where_search IN VARCHAR2) RETURN NUMBER
    AS LANGUAGE JAVA
    NAME 'StringFuctions.findInSet (java.lang.String, java.lang.String)
      return java.lang.int';
");

$installFile = dirname(__FILE__) . DS . 'install-1.5.0.0.php';
if (file_exists($installFile)) {
    include $installFile;
}
