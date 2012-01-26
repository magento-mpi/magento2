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
 * Static class, which helps to work with SQL query.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Helper_Query
{
    /**
     * Obfuscates SQL query (removes all unneeded spaces).
     *
     * @param string|Zend_Db_Select $sql
     * @return string
     */
    public static function compress($sql)
    {
        $sql = (string)$sql;
        $compressedSql = '';
        $sqlLength = function_exists('mb_strlen') ? mb_strlen($sql) : iconv_strlen($sql);
        $insideIdentifier = false;
        $insideString = false;
        $wasAlphanum = false;
        $position = 0;
        while ($position < $sqlLength) {

            $currentChar = $sql{$position};
            if (($currentChar == '"' || $currentChar == '`') && !$insideString) {
                $wasAlphanum = false;
                if ($position + 1 != $sqlLength &&
                    ($sql{$position + 1} == '"' || $sql{$position + 1} == '`') &&
                    $insideIdentifier
                ) {
                    $compressedSql .= $currentChar.$sql{$position+1};
                    $position += 2;
                    continue;
                }
                $insideIdentifier = !$insideIdentifier;
            } elseif ($currentChar == '\\' && $insideString) {
                $wasAlphanum = false;
                if ($position + 1 != $sqlLength && ($sql{$position + 1} == '\\' || $sql{$position + 1} == '\'')) {
                    $compressedSql .= $currentChar.$sql{$position+1};
                    $position += 2;
                    continue;
                }
            } elseif ($currentChar == '\'' && !$insideIdentifier) {
                $wasAlphanum = false;
                $insideString = !$insideString;
            } elseif (!$insideString && !$insideIdentifier) {
                if (ctype_space($currentChar)) {
                    ++$position;
                    continue;
                } elseif (ctype_alnum($currentChar)) {
                    if ($wasAlphanum && ctype_space($sql{$position - 1})) {
                        $compressedSql .= ' ';
                    }
                    $wasAlphanum = true;
                } else {
                    $wasAlphanum = false;
                }
            }

            $compressedSql .= $currentChar;
            ++$position;
        }
        return $compressedSql;
    }
}
