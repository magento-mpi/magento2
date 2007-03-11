<?php
/**
 * Varien Library
 *
 *
 * @category
 * @package
 * @copyright  Copyright (c) 2007 Varien Inc.
 * @license    New BSD License
 * @author     Andrey Korolyov
 * @author     Dmitriy Soroka
 */


require_once 'Varien/Db/Tree/Exception.php';
Zend_Loader::loadClass('Zend_Db_Select');
Zend_Loader::loadClass('Varien_Db_Tree_Node');
Zend_Loader::loadClass('Varien_Db_Tree_NodeSet');

class Varien_Db_Tree
{

    private $_id;
    private $_left;
    private $_right;
    private $_level;
    private $_pid;
    private $_nodesInfo = array();
    
    /**
     * Array of additional tables
     * 
     * array(
     *  [$tableName] => array(
     *              ['joinCondition']
     *              ['fields']
     *          )
     * )
     *
     * @var array
     */
    private $_extTables = array();

    /**
     * Zend_Db_Adapter
     *
     * @var Zend_Db_Adapter_Abstract
     */
    private $_db;

    private $_table;

    function __construct($config = array())
    {
        // set a Zend_Db_Adapter connection
        if (! empty($config['db'])) {

            // convenience variable
            $db = $config['db'];

            // use an object from the registry?
            if (is_string($db)) {
                $db = Zend::registry($db);
            }

            // make sure it's a Zend_Db_Adapter
            if (! $db instanceof Zend_Db_Adapter_Abstract) {
                throw new Varien_Db_Tree_Exception('db object does not implement Zend_Db_Adapter_Abstract');
            }

            // save the connection
            $this->_db = $db;
        } else {
            throw new Varien_Db_Tree_Exception('db object is not set in config');
        }


        if (!empty($config['table'])) {
            $this->setTable($config['table']);
        }

        if (!empty($config['id'])) {
            $this->setIdField($config['id']);
        } else {
            $this->setIdField('id');
        }

        if (!empty($config['left'])) {
            $this->setLeftField($config['left']);
        } else {
            $this->setLeftField('left_key');
        }

        if (!empty($config['right'])) {
            $this->setRightField($config['right']);
        } else {
            $this->setRightField('right_key');
        }

        if (!empty($config['level'])) {
            $this->setLevelField($config['level']);
        } else {
            $this->setLevelField('level');
        }


        if (!empty($config['pid'])) {
            $this->setPidField($config['pid']);
        } else {
            $this->setPidField('parent_id');
        }

    }

    /**
     * set name of id field
     *
     * @param string $name
     * @return Varien_Db_Tree
     */
    public function setIdField($name) {
        $this->_id = $name;
        return $this;
    }

    /**
     * set name of left field
     *
     * @param string $name
     * @return Varien_Db_Tree
     */
    public function setLeftField($name) {
        $this->_left = $name;
        return $this;
    }

    /**
     * set name of right field
     *
     * @param string $name
     * @return Varien_Db_Tree
     */
    public function setRightField($name) {
        $this->_right = $name;
        return $this;
    }

    /**
     * set name of level field
     *
     * @param string $name
     * @return Varien_Db_Tree
     */
    public function setLevelField($name) {
        $this->_level = $name;
        return $this;
    }

    /**
     * set name of pid Field
     *
     * @param string $name
     * @return Varien_Db_Tree
     */
    public function setPidField($name) {
        $this->_pid = $name;
        return $this;
    }


    /**
     * set table name
     *
     * @param string $name
     * @return Varien_Db_Tree
     */
    public function setTable($name) {
        $this->_table = $name;
        return $this;
    }

    function getKeys() {
        $keys = array();
        $keys['id'] = $this->_id;
        $keys['left'] = $this->_left;
        $keys['right'] = $this->_right;
        $keys['pid'] = $this->_pid;
        $keys['level'] = $this->_level;
        return $keys;
    }

    /**
     * Cleare table and add root element
     *
     */
    function clear($data = array())
    {
        // clearing table
        $this->_db->query('TRUNCATE '. $this->_table);
        //$this->_db->delete($this->_table,'');

        // prepare data for root element
        $data[$this->_pid] = 0;
        $data[$this->_left] = 1;
        $data[$this->_right] = 2;
        $data[$this->_level] = 0;

        try  {
            $this->_db->insert($this->_table, $data);
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
        return $this->_db->lastInsertId();
    }


    function getNodeInfo($ID) {
        if (empty($this->_nodesInfo[$ID])) {
            $sql = 'SELECT * FROM '.$this->_table.' WHERE '.$this->_id.'=:id';
            $res = $this->_db->query($sql, array('id' => $ID));
            $data = $res->fetch();
            $this->_nodesInfo[$ID] = $data;
        } else {
            $data = $this->_nodesInfo[$ID];
        }
        return $data;
    }

    function appendChild($ID, $data) {

        if (!$info = $this->getNodeInfo($ID)) {
            return false;
        }
    
        $data[$this->_left]  = $info[$this->_right];
        $data[$this->_right] = $info[$this->_right] + 1;
        $data[$this->_level] = $info[$this->_level] + 1;
        $data[$this->_pid]   = $ID;

        // creating a place for the record being inserted
        if($ID) {
            $this->_db->beginTransaction();
            try {
                $sql = 'UPDATE '.$this->_table.' SET'
                    . ' `'.$this->_left.'` = IF( `'.$this->_left.'` > :left, `'.$this->_left.'`+2, `'.$this->_left.'`),'
                    . ' `'.$this->_right.'` = IF( `'.$this->_right.'`>= :right, `'.$this->_right.'`+2, `'.$this->_right.'`)'
                    . ' WHERE `'.$this->_right.'` >= :right';
                
                $this->_db->query($sql, array('table'=>$this->_table, 'left'=>$info[$this->_left], 'right'=>$info[$this->_right]));
                
                $this->_db->insert($this->_table, $data);
                $this->_db->commit();
            } catch (Exception $e) {
                $this->_db->rollBack();
                echo $e->getMessage();
                echo $sql;
                var_dump($data);
                exit();    
            }
            // TODO: change to ZEND LIBRARY
            $res =  $this->_db->fetchOne('select last_insert_id()');
            return $res;
           //return $this->_db->fetchOne('select last_insert_id()');
            //return $this->_db->lastInsertId();
        }
        return  false;
    }

    public function checkNodes() {
        $sql = $this->_db->select();

        $sql->from(array('t1'=>$this->_table), array('t1.'.$this->_id, new Zend_Db_Expr('COUNT(t1.'.$this->_id.') AS rep')))
        ->from(array('t2'=>$this->_table))
        ->from(array('t3'=>$this->_table), new Zend_Db_Expr('MAX(t3.'.$this->_right.') AS max_right'));


        $sql->where('t1.'.$this->_left.' <> t2.'.$this->_left)
        ->where('t1.'.$this->_left.' <> t2.'.$this->_right)
        ->where('t1.'.$this->_right.' <> t2.'.$this->_right);

        $sql->group('t1.'.$this->_id);
        $sql->having('max_right <> SQRT(4 * rep + 1) + 1');

         
        return $this->_db->fetchAll($sql);
    }

    public function insertBefore($ID, $data) {

    }

    public function removeNode($ID) {
        
        if (!$info = $this->getNodeInfo($ID)) {
            return false;
        }

        if($ID) {
            $this->_db->beginTransaction();
            try {
                // DELETE FROM my_tree WHERE left_key >= $left_key AND right_key <= $right_key
                $this->_db->delete($this->_table, $this->_left.' >= '.$info[$this->_left].' AND '.$this->_right.' <= '.$info[$this->_right]);
                
                // UPDATE my_tree SET left_key = IF(left_key > $left_key, left_key – ($right_key - $left_key + 1), left_key), right_key = right_key – ($right_key - $left_key + 1) WHERE right_key > $right_key
                $sql = 'UPDATE '.$this->_table.' 
					SET 
						'.$this->_left.' = IF('.$this->_left.' > '.$info[$this->_left].', '.$this->_left.' – ('. $info[$this->_right] - $info[$this->_left] + 1 .'), '.$this->_left.'), 
						'.$this->_right.' = '.$this->_right.' – ('. $info[$this->_right] - $info[$this->_left] + 1 .')
                	WHERE 
						'.$this->_right.' > '.$info[$this->_right];
                
               
                $this->_db->commit();
                 var_dump($sql);
                return new Varien_Db_Tree_Node($info, $this->getKeys());;
            } catch (Exception $e) {
                $this->_db->rollBack();
                echo $e->getMessage();
            }
        }
    }

    public function moveBranch($ID, $newID) {

    }
    
    public function addTable($tableName, $joinCondition, $fields='*')
    {
        $this->_extTables[$tableName] = array(
           'joinCondition' => $joinCondition,
           'fields'        => $fields
        );
    }
    
    protected function _addExtTablesToSelect(Zend_Db_Select &$select)
    {
        foreach ($this->_extTables as $tableName=>$info) {
            $select->joinInner($tableName, $info['joinCondition'], $info['fields']);
        }
    }
    
    function getChildren($ID, $start_level = 0, $end_level = 0) 
    {
        try {
            $info = $this->getNodeInfo($ID);
        } catch (Exception $e) {
            echo $e->getMessage();
            exit;
        }
        
        $dbSelect = new Zend_Db_Select($this->_db);
        $dbSelect->from($this->_table)
            ->where($this->_left  . ' >= :left')
            ->where($this->_right . ' <= :right')
            ->order($this->_left);
        
        $this->_addExtTablesToSelect($dbSelect);
        
        $data = array();
        $data['left'] = $info[$this->_left];
        $data['right'] = $info[$this->_right];

        if (!empty($start_level) && empty($end_level)) {
            $dbSelect->where($this->_level . ' = :minLevel');
            $data['minLevel'] = $info[$this->_level] + $start_level;
        }
        
        //echo $dbSelect->__toString();
        $data = $this->_db->fetchAll($dbSelect, $data);

        $nodeSet = new Varien_Db_Tree_NodeSet();
        foreach ($data as $node) {
             $nodeSet->addNode(new Varien_Db_Tree_Node($node, $this->getKeys()));
        }
        return $nodeSet;
    }
    
    function getNode($nodeId)
    {
        $dbSelect = new Zend_Db_Select($this->_db);
        $dbSelect->from($this->_table)
            ->where($this->_table.'.'.$this->_id  . ' >= :id');
        
        $this->_addExtTablesToSelect($dbSelect);
        
        $data = array();
        $data['id'] = $nodeId;
        
        $data = $this->_db->fetchRow($dbSelect, $data);
        
        return new Varien_Db_Tree_Node($data, $this->getKeys());
    }
}
