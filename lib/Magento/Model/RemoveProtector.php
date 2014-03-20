<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Model;

class RemoveProtector implements RemoveProtectorInterface
{
    /**
     * @var array
     */
    protected $protectedModels;

    public function __construct(array $protectedModels = array())
    {
        $this->protectedModels = $protectedModels;
    }

    /**
     * Safeguard function that checks if item can be removed
     *
     * @param AbstractModel $model
     * @return bool
     */
    public function canBeRemoved(AbstractModel $model)
    {
        if (in_array($this->getBaseClassName($model), $this->protectedModels)) {
            return false;
        }

        return true;
    }

    /**
     * Get clean model name without Interceptor and Proxy part and slashes
     * @param object $object
     * @return mixed
     */
    protected function getBaseClassName($object)
    {
        $className = ltrim(get_class($object), "\\");
        $className = str_replace(array('\Interceptor', '\Proxy'), array(''), $className);

        return $className;
    }
}
