<?php
/**
 * {license_notice}
 *
 * @category   Magento
 * @package    Magento_DB
 * @copyright  {copyright}
 * @license    {license_link}
 */


namespace Magento\DB\Tree;

use Magento\DB\Tree\Node\NodeException;

class Node {

    /**
     * @var int
     */
    private $left;

    /**
     * @var int
     */
    private $right;

    /**
     * @var string|int
     */
    private $id;

    /**
     * @var string|int
     */
    private $pid;

    /**
     * @var int
     */
    private $level;

    /**
     * @var string
     */
    private $title;

    /**
     * @var array
     */
    private $data;

    /**
     * @var bool
     */
    public $hasChild = false;

    /**
     * @var float|int
     */
    public $numChild = 0;


    /**
     * @param array $nodeData
     * @param array $keys
     * @return $this
     * @throws NodeException
     */
    function __construct($nodeData, $keys) {
        if (empty($nodeData)) {
            throw new NodeException('Empty array of node information');
        }
        if (empty($keys)) {
            throw new NodeException('Empty keys array');
        }

        $this->id    = $nodeData[$keys['id']];
        $this->pid   = $nodeData[$keys['pid']];
        $this->left  = $nodeData[$keys['left']];
        $this->right = $nodeData[$keys['right']];
        $this->level = $nodeData[$keys['level']];

        $this->data  = $nodeData;
        $a = $this->right - $this->left;
        if ($a > 1) {
            $this->hasChild = true;
            $this->numChild = ($a - 1) / 2;
        }
        return $this;
    }

    /**
     * @param string $name
     * @return null|array
     */
    function getData($name) {
        if (isset($this->data[$name])) {
            return $this->data[$name];
        } else {
            return null;
        }
    }

    /**
     * @return int
     */
    function getLevel() {
        return $this->level;
    }

    /**
     * @return int
     */
    function getLeft() {
        return $this->left;
    }

    /**
     * @return int
     */
    function getRight() {
        return $this->right;
    }

    /**
     * @return string|int
     */
    function getPid() {
        return $this->pid;
    }

    /**
     * @return string|int
     */
    function getId() {
        return $this->id;
    }
    
    /**
     * Return true if node has child
     *
     * @return bool
     */
    function isParent() {
        if ($this->right - $this->left > 1) {
            return true;
        } else {
            return false;
        }
    }
}
