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
 * Local DB query processor for DELETE queries.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_Db_Query_Delete extends Mage_PHPUnit_Db_Query_Abstract implements Mage_PHPUnit_Db_Query_Interface
{
    /**
     * Parses and sets fixture data with queries information to a container
     *
     * @param SimpleXMLElement $fixture
     */
    public function setFixtureData($fixture)
    {
        if ($fixture->deletions) {
            foreach ($fixture->deletions->children() as $selectNode) {
                if ($selectNode->query) {
                    $query = Mage_PHPUnit_Db_Helper_Query::compress((string)$selectNode->query);
                    $resultItem = $this->_getResultItem();
                    if ($selectNode->error) {
                        $resultItem->setErrorMessage((string)$selectNode->error);
                    } else {
                        $rows = trim((string)$selectNode->rows);
                        $resultItem->setResult($rows ? $rows : 0);
                    }
                    $this->_data[$query] = $resultItem;
                }
            }
        }
    }

    /**
     * DELETE-query result taken from fixture
     *
     * @param string $sql
     * @param array $bind
     * @return int CountOfRows
     */
    protected function _getDataByQuery($sql, $bind = array())
    {
        return isset($this->_data[$sql]) ? $this->_data[$sql]->getResult() : 0;
    }

    /**
     * Returns table name by SQL query
     *
     * @param string $sql
     * @throws Mage_PHPUnit_Db_Exception
     * @return string
     */
    protected function _getTableName($sql)
    {
        $result = array();
        preg_match('/^DELETE.*?[\\W]+FROM[\\W]+([0-9a-zA-Z_-]+)(?:(?:[\\W]+.*)|$)/is', $sql, $result);
        if ($result && $result[1]) {
            return $result[1];
        }
        throw new Mage_PHPUnit_Db_Exception('Cannot get tablename from DELETE query');
    }

    /**
     * Checks if this query processor can process passed SQL query.
     *
     * @param string $sql
     * @return bool
     */
    public function test($sql)
    {
        return strtoupper(substr($sql, 0, 6)) == 'DELETE';
    }
}
