<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Tools\Dependency;

/**
 * Dependency
 */
class Dependency
{
    /**#@+
     * Dependencies types
     */
    const TYPE_HARD = 'hard';
    const TYPE_SOFT = 'soft';
    /**#@-*/

    /**
     * Module we depend on
     *
     * @var string
     */
    private $module;

    /**
     * Dependency type
     *
     * @var string
     */
    private $type;

    /**
     * Dependency construct
     *
     * @param string $module
     * @param string $type One of self::TYPE_* constants
     */
    public function __construct($module, $type = self::TYPE_HARD)
    {
        $this->module = $module;

        if ($type) {
            $this->type = (self::TYPE_HARD == $type) ? self::TYPE_HARD : self::TYPE_SOFT;
        } else {
            $this->type = self::TYPE_HARD;
        }
    }

    /**
     * Get module
     *
     * @return string
     */
    public function getModule()
    {
        return $this->module;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Check is hard dependency
     *
     * @return bool
     */
    public function isHard()
    {
        return self::TYPE_HARD == $this->getType();
    }
}
