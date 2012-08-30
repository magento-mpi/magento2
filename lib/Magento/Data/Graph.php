<?php
/**
 * Graph data structure
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Data_Graph
{
    /**
     * Registry of nodes
     *
     * @var array
     */
    protected $_nodes = array();

    /**
     * Declared relations directed "from" "to"
     *
     * @var array
     */
    protected $_from = array();

    /**
     * Inverse relations "to" "from"
     *
     * @var array
     */
    protected $_to = array();

    /**
     * Validate consistency of the declared structure and assign it to the object state
     *
     * @param array $nodes plain array with node identifiers
     * @param array $relations array of 2-item plain arrays, which represent relations of nodes "from" "to"
     */
    public function __construct(array $nodes, array $relations)
    {
        foreach ($nodes as $node) {
            $this->_assertNode($node, false);
            $this->_nodes[$node] = $node;
        }
        foreach ($relations as $pair) {
            list($from, $to) = $pair;
            $this->addRelation($from, $to);
        }
    }

    /**
     * Set a relation between nodes
     *
     * @param mixed $from
     * @param mixed $to
     * @return Magento_Data_Graph
     * @throws InvalidArgumentException
     */
    public function addRelation($from, $to)
    {
        if ($from == $to) {
            throw new InvalidArgumentException("Graph node '{$from}' is linked to itself.");
        }
        $this->_assertNode($from, true);
        $this->_assertNode($to, true);
        $this->_from[$from][$to] = $to;
        $this->_to[$to][$from] = $from;
        return $this;
    }

    /**
     * Export relations between nodes. Can return inverse relations
     *
     * @param bool $inverse
     * @return array
     */
    public function getRelations($inverse = false)
    {
        return $inverse ? $this->_to : $this->_from;
    }

    /**
     * Find a cycle in the graph
     *
     * Returns first found cycle
     * Optionally may specify a node to return a cycle if it is in any
     *
     * @param mixed $node
     * @return array
     */
    public function findCycle($node = null)
    {
        $nodes = (null === $node) ? $this->_nodes : array($node);
        $result = array();
        foreach ($nodes as $node) {
            $result = $this->dfs($node, $node);
            if ($result) {
                break;
            }
        }
        return $result;
    }

    /**
     * "Depth-first search" of a path between nodes
     *
     * Returns path as array of nodes or empty array if path does not exist.
     * Only first found path is found. It will be not necessary the shortest or optimal in any way.
     *
     * @param mixed $from
     * @param mixed $to
     * @param bool $directional
     * @return array
     */
    public function dfs($from, $to, $directional = true)
    {
        $this->_assertNode($from, true);
        $this->_assertNode($to, true);
        $result = $this->_dfs($from, $to, $this->_from);
        if (!$result && !$directional) {
            $result = $this->_dfs($from, $to, $this->_to);
        }
        return $result;
    }

    /**
     * Recursive sub-routine of dfs()
     *
     * @param mixed $from
     * @param mixed $to
     * @param array $graph
     * @param array &$visited
     * @param array $stack
     * @return array
     * @link http://en.wikipedia.org/wiki/Depth-first_search
     */
    protected function _dfs($from, $to, $graph, &$visited = array(), $stack = array())
    {
        $stack[] = $from;
        $visited[$from] = $from;
        if (isset($graph[$from][$to])) {
            $stack[] = $to;
            return $stack;
        }
        if (isset($graph[$from])) {
            foreach ($graph[$from] as $node) {
                if (!isset($visited[$node])) {
                    $result = $this->_dfs($node, $to, $graph, $visited, $stack);
                    if ($result) {
                        return $result;
                    }
                }
            }
        }
        return array();
    }

    /**
     * Verify existence or non-existence of a node
     *
     * @param mixed $node
     * @param bool $mustExist
     * @throws InvalidArgumentException according to assertion rules
     */
    protected function _assertNode($node, $mustExist)
    {
        if (isset($this->_nodes[$node])) {
            if (!$mustExist) {
                throw new InvalidArgumentException("Graph node '{$node}' already exists'.");
            }
        } else {
            if ($mustExist) {
                throw new InvalidArgumentException("Graph node '{$node}' does not exist.");
            }
        }
    }
}
