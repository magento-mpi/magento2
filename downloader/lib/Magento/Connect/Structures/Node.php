<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Connect
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Connect\Structures;

use Magento\Connect\Structures\Graph;

class Node
{
    /**
     * @var mixed
     */
    protected $_data = null;

    /**
     * @var array
     */
    protected $_metadata = array();

    /**
     * @var array
     */
    protected $_arcs = array();

    /**
     * @var Graph
     */
    protected $_graph = null;

    /**
     * Node graph getter
     *
     * @return Graph
     */
    public function &getGraph() 
    {
        return $this->_graph;
    }

    /**
     * Node graph setter.
     * This method should not be called directly.
     * Use Graph::addNode instead.
     *
     * @param Graph &$graph
     * @return void
     */
    public function setGraph(&$graph) 
    {
        $this->_graph =& $graph;
    }

    /**
     * Node data getter.
     *
     * Each graph node can contain a reference to one variable. This is the getter for that reference.
     *
     * @return mixed Data stored in node
     * @access public
     */
    public function &getData() 
    {
        return $this->_data;
    }

    /**
     * Node data setter
     *
     * Each graph node can contain a reference to one variable. This is the setter for that reference.
     *
     * @param mixed $data Data to store in node
     * @return void
     */
    public function setData($data) 
    {
        $this->_data =& $data;
    }

    /**
     * Test for existence of metadata under a given key.
     *
     * @param string  $key Key to test
     * @return bool
     * @access public
     */
    public function metadataKeyExists($key) 
    {
        return array_key_exists($key, $this->_metadata);
    }

    /**
     * Get node metadata
     *
     * @param string  $key
     * @param bool $nullIfNonexistent (defaults to false).
     * @return mixed
     * @throws \Exception
     */
    public function & getMetadata($key, $nullIfNonexistent = false) 
    {
        if (array_key_exists($key, $this->_metadata)) {
            return $this->_metadata[$key];
        } elseif ($nullIfNonexistent) {
                $a = null;
                return $a;
        } else {
            throw new \Exception(__METHOD__." : requested key doesn't exist: {$key}");            
        }
    }

    /**
     * Delete metadata by key
     *
     * @param string $key Key
     * @return void
     */
    public function unsetMetadata($key) 
    {
        if (array_key_exists($key, $this->_metadata)) {
            unset($this->_metadata[$key]);
        }    
        
    }

    /**
     * Node metadata setter
     *
     * Each graph node can contain multiple 'metadata' entries, each stored under a different key, as in an
     * associative array or in a dictionary. This method stores data under the given key. If the key already exists,
     * previously stored data is discarded.
     *
     * @param string  $key
     * @param mixed   $data
     * @return void
     */
    public function setMetadata($key, $data) 
    {
        $this->_metadata[$key] =& $data;
    }

    /**
     * @param mixed &$destinationNode
     * @return void
     */
    protected function _connectTo(&$destinationNode) 
    {
        $this->_arcs[] =& $destinationNode;
    }


    /**
     * Connect this node to another one.
     * If the graph is not directed, the reverse arc, connecting $destinationNode to $this is also created.
     *
     * @param \Magento\Object &$destinationNode  Structures_Graph Node to connect to
     * @return void
     * @throws \Exception
     */
    public function connectTo(&$destinationNode)
    {
        $class = get_class($this);
        if(!$destinationNode instanceof $class) {
            throw new \Exception(__METHOD__." : argument should be instance of {$class}");
        }
         
        // Nodes must already be in graphs to be connected
        if ($this->_graph == null) {
            throw new \Exception(__METHOD__." : tried to connect to null graph");
        }

        if ($destinationNode->getGraph() == null) {
            throw new \Exception(__METHOD__." : tried to connect to node that is not connected to any graph");
        }

        // Connect here
        $this->_connectTo($destinationNode);
        // If graph is undirected, connect back
        if (!$this->_graph->isDirected()) {
            $destinationNode->_connectTo($this);
        }
    }

    /**
     * Return nodes connected to this one.
     * @return array
     */
    public function getNeighbours()
    {
        return $this->_arcs;
    }

    /**
     * Test whether this node has an arc to the target node
     * Returns true if the two nodes are connected
     *
     * @param Node &$target
     * @return bool
     */
    public function connectsTo(&$target)
    {
        $arcKeys = array_keys($this->_arcs);
        foreach($arcKeys as $key) {
            $arc =& $this->_arcs[$key];
            if ($target === $arc) { 
                return true;
            }
        }
        return false;
    }

    /**
     * Calculate the in degree of the node.
     *
     * The indegree for a node is the number of arcs 
     * entering the node. 
     * 
     * For non directed graphs:
     *  always outdegree = indegree.
     *  
     * @return int
     */
    public function inDegree() 
    {
        $result = 0;

        if ($this->_graph == null) {
            return $result;
        }
        if (!$this->_graph->isDirected()) {
            return $this->outDegree();
        }

        $graphNodes =& $this->_graph->getNodes();
        foreach (array_keys($graphNodes) as $key) {
            if ($graphNodes[$key]->connectsTo($this)) {
                $result++;
            }
        }
        return $result;

    }

    /**
     * Calculate the out degree of the node.
     *
     * The outdegree for a node is the number of arcs exiting the node. 
     * For non directed graphs:
     *  always outdegree = indegree.
     *
     * @return int
     */
    public function outDegree()
    {
        if ($this->_graph == null) {
            return 0;
        }
        return count($this->_arcs);
    }

}
