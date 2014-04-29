<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Test\Legacy\Magento\Framework\ObjectManager;

class DiConfigTest extends \PHPUnit_Framework_TestCase
{
    public function testObsoleteDiFormat()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            array($this, 'assertObsoleteFormat'),
            \Magento\TestFramework\Utility\Files::init()->getDiConfigs(true)
        );
    }

    /**
     * Scan the specified di.xml file and assert that it has no obsolete nodes
     *
     * @param string $file
     */
    public function assertObsoleteFormat($file)
    {
        $xml = simplexml_load_file($file);
        $this->assertSame(
            array(),
            $xml->xpath('//param'),
            'The <param> node is obsolete. Instead, use the <argument name="..." xsi:type="...">'
        );
        $this->assertSame(
            array(),
            $xml->xpath('//instance'),
            'The <instance> node is obsolete. Instead, use the <argument name="..." xsi:type="object">'
        );
        $this->assertSame(
            array(),
            $xml->xpath('//array'),
            'The <array> node is obsolete. Instead, use the <argument name="..." xsi:type="array">'
        );
        $this->assertSame(
            array(),
            $xml->xpath('//item[@key]'),
            'The <item key="..."> node is obsolete. Instead, use the <item name="..." xsi:type="...">'
        );
        $this->assertSame(
            array(),
            $xml->xpath('//value'),
            'The <value> node is obsolete. Instead, provide the actual value as a text literal.'
        );
    }
}
