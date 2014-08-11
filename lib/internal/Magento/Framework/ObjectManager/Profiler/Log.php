<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Profiler;

use Magento\Framework\ObjectManager\Profiler\Tree\Item as Item;

class Log
{
    protected $objects;

    protected static $instance;


    /**
     * @var Item
     */
    protected $currentItem = null;

    protected $data = array();

    protected $roots = array();

    protected $used = array();

    public function __construct()
    {
        register_shutdown_function(array($this, 'display'));
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Log();
        }
        return self::$instance;
    }


    public function startCreating($class)
    {
        $parent = empty($this->currentItem) ? null : $this->currentItem;
        $item = new Item($class, $parent);

        if (!$parent) {
            $this->roots[] = $item;
        }

        $this->currentItem = $item;
    }

    public function stopCreating($object)
    {
        $this->currentItem->setHash(spl_object_hash($object));
        $this->currentItem = $this->currentItem->getParent();
    }

    public function add($object)
    {
        if ($this->currentItem) {
            $item = new Item(get_class($object), $this->currentItem);
            $this->currentItem->addChild($item);
        } else {
            $item = new Item(get_class($object), null);
            $this->roots[] = $item;
        }
        $item->setHash(spl_object_hash($object));

    }

    public function invoked($object)
    {
        $this->used[spl_object_hash($object)] = 1;
    }

    public function display()
    {
        echo '<table border="1" cellspacing="0" cellpadding="2">' . PHP_EOL;
        echo '<caption>Creation chain (Red items are never used)</caption>>';
        echo '<tbody>';
        echo "<tr><th>Instance class</th></tr>";
        foreach ($this->roots as $root) {
            $this->displayItem($root);
        }
        echo '</tbody>';
        echo '</table>';
    }

    protected function displayItem(Item $item, $level = 0)
    {
        $colorStyle = isset($this->used[$item->getHash()]) ? '' : ' style="color:red" ';

        echo "<tr><td $colorStyle>" . str_repeat('·&nbsp;', $level) . $item->getClass() . ' - ' . $item->getHash() . '</td></tr>';

        foreach ($item->getChildren() as $child) {
            $this->displayItem($child, $level + 1);
        }

    }
}
