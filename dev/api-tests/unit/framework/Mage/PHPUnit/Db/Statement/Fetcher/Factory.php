<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Statement fetcher factory. Gets fetcher by style.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Statement_Fetcher_Factory
{
    /**
     * Fetchers style<->class associations.
     *
     * @var array
     */
    static protected $_fetchers = array(
        Zend_Db::FETCH_ASSOC => 'Mage_PHPUnit_Db_Statement_Fetcher_Assoc',
        Zend_Db::FETCH_NUM => 'Mage_PHPUnit_Db_Statement_Fetcher_Num',
        Zend_Db::FETCH_BOTH => 'Mage_PHPUnit_Db_Statement_Fetcher_Both'
    );

    static protected $_fetchersCache = array();

    /**
     * Returns fetcher object
     *
     * @param int|null $style
     * @return Mage_PHPUnit_Db_Statement_Fetcher_Interface
     */
    static public function getFetcher($style = null)
    {
        if (is_null($style)) {
            $style = Zend_Db::FETCH_ASSOC; //default = ASSOC
        }
        if (!isset(self::$_fetchersCache[$style])) {
            $class = self::_getFetcherClassName($style);
            self::$_fetchersCache[$style] = new $class();
        }

        return self::$_fetchersCache[$style];
    }

    /**
     * Returns fetcher class name
     *
     * @param int|null $style
     * @return string
     * @throws Exception
     */
    static protected function _getFetcherClassName($style)
    {
        if (!empty(self::$_fetchers[$style])) {
            return self::$_fetchers[$style];
        }
        throw new Exception(sprintf('Fetcher does not exists for selected fetchStyle: %s', $style));
    }
}
