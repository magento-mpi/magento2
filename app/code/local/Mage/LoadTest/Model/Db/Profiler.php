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
 * @category   Mage
 * @package    Mage_LoadTest
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_LoadTest_Model_Db_Profiler extends Zend_Db_Profiler
{

    /**
     * Starts a query.  Creates a new query profile object (Zend_Db_Profiler_Query)
     * and returns the "query profiler handle".  Run the query, then call
     * queryEnd() and pass it this handle to make the query as ended and
     * record the time.  If the profiler is not enabled, this takes no
     * action and immediately returns null.
     *
     * @param  string  $queryText   SQL statement
     * @param  integer $queryType   OPTIONAL Type of query, one of the Zend_Db_Profiler::* constants
     * @return integer|null
     */
    public function queryStart($queryText, $queryType = null)
    {
        return parent::queryStart($queryText, $queryType);
    }

}
