<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   {copyright}
 * @license     {license_link}
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
