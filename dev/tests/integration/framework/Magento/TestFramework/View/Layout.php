<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TestFramework\View;

class Layout extends \Magento\View\Layout
{
    /**
     * @var bool
     */
    protected $isCacheable = true;

    /**
     * @return bool
     */
    public function isCacheable()
    {
        return $this->isCacheable && parent::isCacheable();
    }

    /**
     * @param bool $isCacheable
     * @return void
     */
    public function setIsCacheable($isCacheable)
    {
        $this->isCacheable = $isCacheable;
    }
}
