<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CatalogSearch Mysql resource helper model
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Resource_Helper_Mysql4 extends Mage_Eav_Model_Resource_Helper_Mysql4
{

    /**
     * Join information for usin full text search
     *
     * @param  Magento_DB_Select $select
     * @return Magento_DB_Select $select
     */
    public function chooseFulltext($table, $alias, $select)
    {
        $field = new Zend_Db_Expr('MATCH ('.$alias.'.data_index) AGAINST (:query IN BOOLEAN MODE)');
        $select->columns(array('relevance' => $field));
        return $field;
    }

    /**
     * Prepare Terms
     *
     * @param string $str The source string
     * @return array(0=>words, 1=>terms)
     */
    function prepareTerms($str, $maxWordLength = 0)
    {
        $boolWords = array(
            '+' => '+',
            '-' => '-',
            '|' => '|',
            '<' => '<',
            '>' => '>',
            '~' => '~',
            '*' => '*',
        );
        $brackets = array(
            '('       => '(',
            ')'       => ')'
        );
        $words = array(0=>"");
        $terms = array();
        preg_match_all('/([\(\)]|[\"\'][^"\']*[\"\']|[^\s\"\(\)]*)/uis', $str, $matches);
        $isOpenBracket = 0;
        foreach ($matches[1] as $word) {
            $word = trim($word);
            if (strlen($word)) {
                $word = str_replace('"', '', $word);
                $isBool = in_array(strtoupper($word), $boolWords);
                $isBracket = in_array($word, $brackets);
                if (!$isBool && !$isBracket) {
                    $terms[$word] = $word;
                    $word = '"'.$word.'"';
                    $words[] = $word;
                } else if ($isBracket) {
                    if ($word == '(') {
                        $isOpenBracket++;
                    } else {
                        $isOpenBracket--;
                    }
                    $words[] = $word;
                } else if ($isBool) {
                    $words[] = $word;
                }
            }
        }
        if ($isOpenBracket > 0) {
            $words[] = sprintf("%')".$isOpenBracket."s", '');
        } else if ($isOpenBracket < 0) {
            $words[0] = sprintf("%'(".$isOpenBracket."s", '');
        }
        if ($maxWordLength && count($terms) > $maxWordLength) {
            $terms = array_slice($terms, 0, $maxWordLength);
        }
        $result = array($words, $terms);
        return $result;
    }

    /**
     * Use sql compatible with Full Text indexes
     *
     * @param mixed $table The table to insert data into.
     * @param array $data Column-value pairs or array of column-value pairs.
     * @param arrat $fields update fields pairs or values
     * @return int The number of affected rows.
     */
    public function insertOnDuplicate($table, array $data, array $fields = array()) {
        return $this->_getWriteAdapter()->insertOnDuplicate($table, $data, $fields);
    }
}
