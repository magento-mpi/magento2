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
 * Local DB query processor for SELECT queries.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Query_Select extends Mage_PHPUnit_Db_Query_Abstract implements Mage_PHPUnit_Db_Query_Interface
{
    /**
     * Parses and sets fixture data with queries information to a container
     *
     * @param SimpleXMLElement $fixture
     */
    public function setFixtureData($fixture)
    {
        if ($fixture->selects) {
            foreach ($fixture->selects->children() as $selectNode) {
                if ($selectNode->query) {
                    $query = $this->_compressQuery((string)$selectNode->query);
                    $resultItem = $this->_getResultItem();
                    if ($selectNode->error) {
                        $resultItem->setErrorMessage((string)$selectNode->error);
                    } elseif ($selectNode->rows) {
                        $rows = array();
                        foreach ($selectNode->rows->children() as $rowNode) {
                            $row = array();
                            foreach ($rowNode->children() as $fieldNode) {
                                $row[$fieldNode->getName()] = (string)$fieldNode;
                            }
                            $rows[] = $row;
                        }
                        $resultItem->setResult($rows);
                    }
                    $this->_data[$query] = $resultItem;
                }
            }
        }
    }

    /**
     * Select-query result taken from fixture
     *
     * @param string $sql
     * @param array $bind
     * @return array array(array('field' => value, ...), array('field' => value, ...), ...)
     */
    protected function _getDataByQuery($sql, $bind = array())
    {
        return isset($this->_data[$sql]) ? $this->_data[$sql]->getResult() : array();
    }

    /**
     * Returns table name by SQL query
     *
     * @param string|Zend_Db_Select $sql
     * @throws Mage_PHPUnit_Db_Exception
     * @return string
     */
    protected function _getTableName($sql)
    {
        if (is_string($sql)) {
            $result = array();
            preg_match('/^SELECT.*?[\\W]+FROM[\\W]+([0-9a-zA-Z_-]+)(?:(?:[\\W]+.*)|$)/is', (string)$sql, $result);
            if ($result && $result[1]) {
                return $result[1];
            }
        } elseif ($sql instanceof Zend_Db_Select) {
            $from = $sql->getPart(Zend_Db_Select::FROM);
            $keys = array_keys($from);
            $tableName = $from[$keys[0]]['tableName'];
            return $tableName;
        }
        throw new Mage_PHPUnit_Db_Exception('Cannot get tablename from SELECT query');
    }

    /**
     * Checks if this query processor can process passed SQL query.
     *
     * @param string $sql
     * @return bool
     */
    public function test($sql)
    {
        return strtoupper(substr((string)$sql, 0, 6)) == 'SELECT';
    }
}
