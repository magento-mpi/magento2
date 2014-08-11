<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Profiler;

class Log
{
    protected $objects;

    protected static $instance;

    protected $stack = array();

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
        array_push($this->stack, $class);
    }

    public function stopCreating()
    {
        array_pop($this->stack);
    }

    public function add($object)
    {
        $requestedFrom = count($this->stack) ? $this->stack[count($this->stack) - 1] : '';
        $this->objects[get_class($object)][spl_object_hash($object)] = array(0, $requestedFrom);
    }

    public function invoked($object)
    {
        $class = get_class($object);
        $this->objects[$class][spl_object_hash($object)][0]++;
    }

    public function display()
    {
        echo "<h3>Unused object list</h3>";
        echo "<table>";
        echo "<tr><th>Instance class</th><th>Requested from</th></tr>";
        $totalUnused = $totalCreated = 0;
        foreach ($this->objects as $class => $instances) {
            foreach ($instances as $instance) {
                $totalCreated ++;
                if (!$instance[0]) {
                    $totalUnused ++;
                    echo "<tr><td>$class</td><td>$instance[1]</td></tr>";
                }
            }
        }
        echo "</table>";
        echo "Total unused : $totalUnused of $totalCreated";
    }
}
