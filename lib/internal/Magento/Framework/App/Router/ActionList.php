<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Router;

class ActionList
{
    /**
     * List of application actions
     *
     * @var array
     */
    protected $actions;

    /**
     * @var array
     */
    protected $reservedWords;

    /**
     * @param \Magento\Framework\Config\CacheInterface $cache
     * @param ActionList\Reader $actionReader
     * @param string $actionInterface
     * @param string $cacheKey
     * @param array $reservedWords
     */
    public function __construct(
        \Magento\Framework\Config\CacheInterface $cache,
        ActionList\Reader $actionReader,
        $actionInterface = '\Magento\Framework\App\ActionInterface',
        $cacheKey = 'app_action_list',
        $reservedWords = array('new', 'switch', 'return', 'print', 'list')
    ) {
        $this->reservedWords = $reservedWords;
        $this->actionInterface = $actionInterface;
        $data = $cache->load($cacheKey);
        if (!$data) {
            $this->actions = $actionReader->read();
            $cache->save(serialize($this->actions), $cacheKey);
        } else {
            $this->actions = unserialize($data);
        }
    }

    /**
     * Retrieve action class
     *
     * @param string $module
     * @param string $area
     * @param string $namespace
     * @param string $action
     * @return null|string
     */
    public function get($module, $area, $namespace, $action)
    {
        if ($area) {
            $area = '\\' . $area;
        }
        if (in_array(strtolower($action), $this->reservedWords)) {
            $action .= 'action';
        }
        $fullPath = str_replace('_', '\\', strtolower(
            $module . '\\controller' . $area . '\\' . $namespace . '\\' . $action
        ));
        if (isset($this->actions[$fullPath])) {
            return is_subclass_of($this->actions[$fullPath], $this->actionInterface) ? $this->actions[$fullPath] : null;
        }
        return null;
    }
}
