<?php

/**
 * Category tree model
 *
 * @package    Ecom
 * @subpackage Catalog
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Catalog_Model_Category_Tree extends Mage_Catalog_Model_Mysql4
{
    /**
     * DB tree object
     *
     * @var Varien_Db_Tree
     */
    private $_dbTree;

    public function __construct()
    {
        parent::__construct();

        $treeTable = Mage::registry('resources')->getTableName('catalog', 'category');

        $config = array();
        $config['db']   = $this->_read;
        $config['table']= $treeTable;
        $config['id']   = 'category_id';

        try {
            $this->_dbTree = new Varien_Db_Tree($config);
            $this->_dbTree->setTable($config['table'])
                ->setLeftField('left_key')
                ->setRightField('right_key')
                ->setPidField('pid')
                ->setLevelField('level');

            $attributeValueTable = Mage::registry('resources')->getTableName('catalog', 'category_attribute_value');
            $condition = "$attributeValueTable.category_id=$treeTable.category_id
                          AND $attributeValueTable.website_id=".Mage_Core_Environment::getCurentWebsite();

            $this->_dbTree->addTable($attributeValueTable, $condition);

            $attributeTable = Mage::registry('resources')->getTableName('catalog', 'category_attribute');
            $condition = "$attributeValueTable.attribute_id=$attributeTable.attribute_id
                          AND $attributeTable.attribute_code='name'";

            $this->_dbTree->addTable($attributeTable, $condition);
        }
        catch (PDOException $e) {
            echo $e->getMessage();
        }
        catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
    }

    function getObject() {
        return $this->_dbTree;
    }

    /**
     * Retrieve tree of attributes (currently categories)
     *
     * @author  Andrey Korolyov <andrey@varien.com>
     * @author  Moshe Gurvich <moshe@varien.com>
     * @param   integer $parent
     * @return  mixed
     */
    function getTree($parent=1)
    {
        try {
            $data = $this->_dbTree->getChildren($parent);
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
        return $data;
    }

    /**
     * Get tree level
     *
     * @param   int $levelID
     * @param   int $startLevel
     * @return  mixed
     */
    function getLevel($levelID, $startLevel=1)
    {
        try {
            $data = $this->_dbTree->getChildren($levelID, $startLevel);
            return $data;
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
            exit();
        }

    }

    /**
     * Remove node From Tree
     *
     * @param integer $id
     * @return Varien_Db_Tree_Node
     */
    function deleteNode($id) {
        return $this->_dbTree->removeNode($id);
    }

    function appendChild($id, $data = array()) {
        $attributeValueTable = Mage::registry('resources')->getTableName('catalog', 'category_attribute_value');
        $data['category_id'] = $this->_dbTree->appendChild($id, array('website_id'=>1));
        $data['website_id'] = 1;
        $data['attribute_id'] = 1;
        $data['attribute_value'] = 'test';
        $this->_write->insert($attributeValueTable, $data);
        $data['attribute_id'] = 2;
        $this->_write->insert($attributeValueTable, $data);
    }

    /**
     * Get tree node
     *
     * @param   int $nodeId
     * @return  mixed
     */
    function getNode($nodeId)
    {
        try {
            $node = $this->_dbTree->getNode($nodeId);
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
        return $node;
    }
}
