<?php
/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: All.php 10013 2008-07-09 21:08:06Z norm2782 $
 */

/**
 * @see Zend_Paginator_ScrollingStyle_Interface
 */
require_once 'Zend/Paginator/ScrollingStyle/Interface.php';

/**
 * A scrolling style that returns every page in the collection.  
 * Useful when it is necessary to make every page available at 
 * once--for example, when using a dropdown menu pagination control.
 * 
 * @category   Zend
 * @package    Zend_Paginator
 * @copyright  Copyright (c) 2005-2008 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Paginator_ScrollingStyle_All implements Zend_Paginator_ScrollingStyle_Interface
{
    /**
     * Returns an array of all pages given a page number and range.
     * 
     * @param  Zend_Paginator $paginator
     * @param  integer $pageRange Unused
     * @return array
     */
    public function getPages(Zend_Paginator $paginator, $pageRange = null)
    {
        return $paginator->getPagesInRange(1, $paginator->count());
    }
}