<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\TestClass;

/**
 * This class is used to test out the formatter. This is not a real class.
 */
class TestClass
{
    // This is a comment in a class.
    // Second comment
    const A_CONSTANT = "\n";

    public function betaCall($alpha, $beta)
    {
        $bigString = <<<LOCALXML
<root>
    <parent>
        <child> {$alpha}
        </child>
    </parent>
</root>
LOCALXML;
        $anotherBigString = <<<LOCALXML2
<vehicle>
    <car>
        <honda>
        </honda>
    </car>
</vehicle>
LOCALXML2;
        echo $alpha . "\n" . $beta . "\n";
        echo 'Here it is\n' . $bigString;
        echo 'And here' . $anotherBigString;
    }

    public function main()
    {
        $this->betaCall(1, 2);
    }
}
