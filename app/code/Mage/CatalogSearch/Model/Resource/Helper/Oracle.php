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
 * CatalogSearch Oracle resource helper model
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Resource_Helper_Oracle extends Mage_Eav_Model_Resource_Helper_Oracle
{

    /**
     * Join information for usin full text search
     *
     * @param  Magento_DB_Select $select
     * @return Magento_DB_Select $select
     */
    public function chooseFulltext($table, $alias, $select)
    {
        $field = new Zend_Db_Expr('SCORE(1)');
        $select->columns(array('relevance'  => $field));
        return new Zend_Db_Expr('CONTAINS ('.$alias.'.data_index, :query, 1 ) > 0');
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
            '&'   => '&',
            'AND' => 'AND',
            '|'   => '|',
            'OR'  => 'OR',
            '!'   => '!',
            'NOT' => 'NOT',
        );
        $brackets = array(
            '('       => '(',
            ')'       => ')'
        );
        $words = array(0 => "");
        $terms = array();
        preg_match_all('/([\(\)]|[\"\'][^"\']*[\"\']|[^\s\"\(\)]*)/uis', $str, $matches);
        $isPrevWord = null;
        $isOpenBracket = 0;
        foreach ($matches[1] as $word) {
            $word = trim($word);
            if (strlen($word)) {
                $word = str_replace('"', '', $word);
                $isBool = in_array(strtoupper($word), $boolWords);
                $isBracket = in_array($word, $brackets);
                if (!$isBool && !$isBracket) {
                    if (!is_null($isPrevWord) && ($isPrevWord == 'term' || $isPrevWord == ')')) {
                        $words[] = 'OR';
                    }
                    $terms[$word] = $word;
                    $word = '"'.$word.'"';
                    $words[] = $word;
                    $isPrevWord = 'term';
                } else if ($isBracket) {
                    if ($isPrevWord == '(') {
                        $words[] = '""';
                        $words[] = 'OR';
                    }
                    if ($isPrevWord == 'term' && $word != ')') {
                        $words[] = 'OR';
                    }
                    if ($word == '(') {
                        $isPrevWord = '(';
                        $isOpenBracket++;
                    } else {
                        $isPrevWord = ')';
                        $isOpenBracket--;
                    }
                    $words[] = $word;
                } else if ($isBool) {
                    if (!is_null($isPrevWord)) {
                        if ($isPrevWord == '(') {
                            $words[] = '""';
                        }
                        if ($isPrevWord == 'predicate') {
                            continue;
                        }
                        $isPrevWord = 'predicate';
                        $words[] = $word;
                    }
                }
            }
        }
        if ($isPrevWord == 'predicate') {
            array_pop($words);
        }
        if ($isOpenBracket > 0) {
            $words[] = sprintf("%')".$isOpenBracket."s", '');
        } else if ($isOpenBracket < 0) {
            $words[0] = sprintf("%'(".$isOpenBracket."s", '');
        }
        if ($maxWordLength && count($terms) > $maxWordLength) {
            $terms = array_slice($terms, 0, $maxWordLength);
        }
        if (count($words) == 1) {
            $words[0] = '""';
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
        return $this->_getWriteAdapter()->insertOnDuplicateCompatible($table, $data, $fields);
    }
}
