<?php
/**
 * {license_notice}
 *
 * @category    Tools
 * @package     unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once realpath(dirname(__FILE__) . '/../../../../../../') . '/Tools/Migration/Acl/Generator.php';

/**
 * Tools_Migration_Acl test case
 */
class Tools_Migration_Acl_GeneratorSaveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var $model Tools_Migration_Acl_Generator
     */
    protected $_model;

    /**
     * @var string
     */
    protected $_fixturePath;

    /**
     * @var string
     */
    protected $_originFile;

    /**
     * @var string
     */
    protected $_aclFile;

    public function setUp()
    {
        if (false == function_exists('tidy_parse_string')) {
            $this->markTestSkipped('Tidy extension is required');
        }

        $this->_model = new Tools_Migration_Acl_Generator();
        $this->_fixturePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files';
        $path = $this->_fixturePath . DIRECTORY_SEPARATOR . 'save' . DIRECTORY_SEPARATOR;

        $this->_originFile = $path . 'adminhtml.xml';
        $this->_aclFile = $path . 'adminhtml' . DIRECTORY_SEPARATOR . 'acl.xml';

        copy($this->_originFile . '.dist', $this->_originFile);

        $comment = PHP_EOL;
        $comment .= '/**' . PHP_EOL;
        $comment .= '* {license_notice}' . PHP_EOL;
        $comment .= '*' . PHP_EOL;
        $comment .= '* @category    Category' . PHP_EOL;
        $comment .= '* @package     Module_Name' . PHP_EOL;
        $comment .= '* @copyright   {copyright}' . PHP_EOL;
        $comment .= '* @license     {license_link}' . PHP_EOL;
        $comment .= '*/' . PHP_EOL;

        $dom = new DOMDocument();
        $comment = $dom->createComment($comment);
        $dom->appendChild($comment);

        $config = $dom->createElement('config');
        $dom->appendChild($config);
        $acl = $dom->createElement('acl');
        $config->appendChild($acl);
        $resources = $dom->createElement('resources');
        $acl->appendChild($resources);

        $resource1 = $dom->createElement('resource');
        $resource1->setAttribute('id', 'Map_Module::admin');
        $resources->appendChild($resource1);

        $resource2 = $dom->createElement('resource');
        $resource2->setAttribute('id', 'Module_One::customer');
        $resource2->setAttribute('title', 'Customers');
        $resource2->setAttribute('sortOrder', '40');
        $resource1->appendChild($resource2);

        $resource3 = $dom->createElement('resource');
        $resource3->setAttribute('id', 'Module_Two::group');
        $resource3->setAttribute('title', 'Customer Groups');
        $resource3->setAttribute('sortOrder', '10');
        $resource2->appendChild($resource3);

        $this->_model->setParsedDomList(array($this->_originFile => $dom));
    }

    public function tearDown()
    {
        unset($this->_model);
        unlink($this->_aclFile);
        rmdir(dirname($this->_aclFile));
        if (file_exists($this->_originFile)) {
            unlink($this->_originFile);
        }
    }

    public function testSaveAclFiles()
    {
        $this->_model->saveAclFiles();
        $this->assertFileExists($this->_aclFile);

        $expectedFilePath = $this->_fixturePath . '/save/save_result.xml';
        $expectedDom = new DOMDocument();
        $expectedDom->load($expectedFilePath);

        $actualDom = new DOMDocument();
        $actualDom->load($this->_aclFile);

        $this->assertEquals($expectedDom->saveXML(), $actualDom->saveXML());
    }
}
