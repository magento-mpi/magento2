<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
//namespace Magento\TestFramework\Listener;

class Magento_TestFramework_Listener_ExtededTestdox extends \PHPUnit_Util_TestDox_ResultPrinter
{
    /**
     * Handler for 'start class' event.
     *
     * @param  string $name
     */
    protected function startClass($name)
    {
        $this->write($this->currentTestClassPrettified . "\n");
    }

    /**
     * Handler for 'on test' event.
     *
     * @param  string $name
     * @param  boolean $success
     */
    protected function onTest($name, $success = true)
    {
        if ($success) {
            $this->write(' [x] ');
        } else {
            $this->write(' [ ] ');
        }

        $this->write($name . "\n");
    }

    /**
     * Handler for 'end class' event.
     *
     * @param  string $name
     */
    protected function endClass($name)
    {
        $this->write("\n");
    }
}