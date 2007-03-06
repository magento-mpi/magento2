<?php



class Mage_Catalog_Model_Mysql4_Categories extends Mage_Core_Model_Mysql4
{

    private $treeObject;


    function __construct() 
    {
        parent::__construct();
        
        $treeTable = $this->_getTableName('catalog_read', 'category');
        
        $config = array();
        $config['db'] = $this->_read;
        $config['table'] = $treeTable;
        $config['id'] = 'category_id';

        try {

            $this->treeObject = new Varien_Db_Tree($config);
            $this->treeObject->setTable($config['table'])
                             ->setLeftField('left_key')
                             ->setRightField('right_key')
                             ->setPidField('pid')
                             ->setLevelField('level');
            
            $extTable = $this->_getTableName('catalog_read', 'category_extension');            
            $condition = "$extTable.category_id=$treeTable.category_id AND $extTable.website_id=".Mage::getCurentWebsite();
            
            $this->treeObject->addTable($extTable, $condition);
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
    }


    /**
     * Retrieve tree of attributes (currently categories)
     *
     * @author Andrey Korolyov <andrey@varien.com>
     * @author Moshe Gurvich <moshe@varien.com>
     * @param integer $parent
     * @return mixed
     */
    function getTree($parent=1)
    {
        try {
            $data = $this->treeObject->getChildren($parent);
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
        return $data;
    }

    function getLevel($levelID, $startLevel=1) 
    {
        try {
            $data = $this->treeObject->getChildren($levelID, $startLevel);
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
        return $data;
    }
    
    function getNode($nodeId)
    {
        try {
            $node = $this->treeObject->getNode($nodeId);
        } catch (PDOException $e) {
            echo $e->getMessage();
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
        }
        return $node;
    }
}