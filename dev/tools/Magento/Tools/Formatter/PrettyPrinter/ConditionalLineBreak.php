<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;


class ConditionalLineBreak extends LineBreak
{
    protected $alternate;

    public function __construct($alternate)
    {
        $this->alternate = $alternate;
    }

    public function __toString() {
        return $this->alternate; // TODO: alternately return an EOL
    }
}