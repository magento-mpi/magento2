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
 * Tools_Migration_Acl_Generator test case
 */
class Tools_Migration_Acl_GeneratorTest extends PHPUnit_Framework_TestCase
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
     * Adminhtml file list
     *
     * @var array
     */
    protected $_adminhtmlFiles = array();

    public function setUp()
    {
        $this->_model = new Tools_Migration_Acl_Generator();
        $this->_fixturePath = realpath(__DIR__) . DIRECTORY_SEPARATOR . '_files';

        $prefix = $this->_fixturePath . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'code' . DIRECTORY_SEPARATOR;
        $suffix = DIRECTORY_SEPARATOR . 'etc' . DIRECTORY_SEPARATOR . 'adminhtml.xml';

        $this->_adminhtmlFiles = array(
            $prefix . 'local' . DIRECTORY_SEPARATOR . 'Namespace' . DIRECTORY_SEPARATOR . 'Module' . $suffix,
            $prefix . 'community' . DIRECTORY_SEPARATOR . 'Namespace' . DIRECTORY_SEPARATOR . 'Module' . $suffix,
            $prefix . 'core' . DIRECTORY_SEPARATOR . 'Enterprise' . DIRECTORY_SEPARATOR . 'Module' . $suffix,
            $prefix . 'core' . DIRECTORY_SEPARATOR . 'Mage' . DIRECTORY_SEPARATOR . 'Module' . $suffix,
        );

        $this->_model->setAdminhtmlFiles($this->_adminhtmlFiles);

        $this->_model->setBasePath($this->_fixturePath);
    }

    public function testGetCommentText()
    {
        $expected = PHP_EOL;
        $expected .= '/**' . PHP_EOL;
        $expected .= '* {license_notice}' . PHP_EOL;
        $expected .= '*' . PHP_EOL;
        $expected .= '* @category    Category' . PHP_EOL;
        $expected .= '* @package     Module_Name' . PHP_EOL;
        $expected .= '* @copyright   {copyright}' . PHP_EOL;
        $expected .= '* @license     {license_link}' . PHP_EOL;
        $expected .= '*/' . PHP_EOL;

        $this->assertEquals($expected, $this->_model->getCommentText('Category', 'Module_Name'));
    }

    /**
     * @param $filePath
     * @param $expectedModuleName
     *
     * @dataProvider getModuleNameDataProvider
     */
    public function testGetModuleName($filePath, $expectedModuleName)
    {
        $this->assertEquals($expectedModuleName, $this->_model->getModuleName($filePath), 'Incorrect Module Name');
    }

    /**
     * @param $filePath
     * @param $expectedCategory
     *
     * @dataProvider getCategoryDataProvider
     */
    public function testGetCategory($filePath, $expectedCategory)
    {
        $this->assertEquals($expectedCategory, $this->_model->getCategory($filePath), 'Incorrect Category Name');
    }

    /**
     * @return array
     */
    public function getModuleNameDataProvider()
    {
        return array(
            array(
                'filePath' => DIRECTORY_SEPARATOR
                    . 'app ' . DIRECTORY_SEPARATOR
                    . 'core ' . DIRECTORY_SEPARATOR
                    . 'Enterprise' . DIRECTORY_SEPARATOR
                    . 'ModuleOne' . DIRECTORY_SEPARATOR
                    . 'etc' . DIRECTORY_SEPARATOR
                    . 'adminhtml.xml',
                'moduleName' => 'Enterprise_ModuleOne',
            ),
            array(
                'filePath' => DIRECTORY_SEPARATOR
                    . 'app ' . DIRECTORY_SEPARATOR
                    . 'core ' . DIRECTORY_SEPARATOR
                    . 'Mage' . DIRECTORY_SEPARATOR
                    . 'ModuleOne' . DIRECTORY_SEPARATOR
                    . 'etc' . DIRECTORY_SEPARATOR
                    . 'adminhtml.xml',
                'moduleName' => 'Mage_ModuleOne',
            ),
        );
    }

    /**
     * @return array
     */
    public function getCategoryDataProvider()
    {
        return array(
            array(
                'filePath' => DIRECTORY_SEPARATOR
                    . 'app ' . DIRECTORY_SEPARATOR
                    . 'core ' . DIRECTORY_SEPARATOR
                    . 'Enterprise' . DIRECTORY_SEPARATOR
                    . 'ModuleOne' . DIRECTORY_SEPARATOR
                    . 'etc' . DIRECTORY_SEPARATOR
                    . 'adminhtml.xml',
                'category' => 'Enterprise',
            ),
            array(
                'filePath' => DIRECTORY_SEPARATOR
                    . 'app ' . DIRECTORY_SEPARATOR
                    . 'core ' . DIRECTORY_SEPARATOR
                    . 'Mage' . DIRECTORY_SEPARATOR
                    . 'ModuleOne' . DIRECTORY_SEPARATOR
                    . 'etc' . DIRECTORY_SEPARATOR
                    . 'adminhtml.xml',
                'category' => 'Mage',
            ),
        );
    }

    public function testIsRestrictedNode()
    {
        $restricted = array(
            'restricted_one',
            'restricted_two',
        );
        $this->_model->setRestrictedNodeNames($restricted);
        $this->assertEquals($restricted, $this->_model->getRestrictedNodeNames());

        $this->assertTrue($this->_model->isRestrictedNode('restricted_one'));
        $this->assertTrue($this->_model->isRestrictedNode('restricted_two'));
        $this->assertFalse($this->_model->isRestrictedNode('restricted_three'));

    }

    public function testIsMetaNode()
    {
        $metaNodes = array(
            'meta_one' => 'MetaOne',
            'meta_two' => 'MetaTwo',
        );
        $this->_model->setMetaNodeNames($metaNodes);
        $this->assertEquals($metaNodes, $this->_model->getMetaNodeNames());

        $this->assertTrue($this->_model->isMetaNode('meta_one'));
        $this->assertTrue($this->_model->isMetaNode('meta_two'));
        $this->assertFalse($this->_model->isMetaNode('meta_three'));
    }

    public function testIsValidNodeType()
    {
        $this->assertFalse($this->_model->isValidNodeType(0));
        $this->assertFalse($this->_model->isValidNodeType(null));
        $this->assertTrue($this->_model->isValidNodeType(1));
    }

    /**
     * @param $expectedPath
     * @param $codePool
     * @param $namespace
     * @dataProvider getEtcPatternDataProvider
     */
    public function testGetEtcPattern($expectedPath, $codePool, $namespace)
    {
        $this->assertStringEndsWith($expectedPath, $this->_model->getEtcDirPattern($codePool, $namespace));
    }

    /**
     * @return array
     */
    public function getEtcPatternDataProvider()
    {
        return array(
            array(
                'expectedPath' => DIRECTORY_SEPARATOR
                    . 'app' . DIRECTORY_SEPARATOR
                    . 'code' . DIRECTORY_SEPARATOR
                    . '*' . DIRECTORY_SEPARATOR
                    . '*' . DIRECTORY_SEPARATOR
                    . '*' . DIRECTORY_SEPARATOR
                    . 'etc' . DIRECTORY_SEPARATOR,
                'codePool' => '*',
                'namespace' => '*',
            ),
            array(
                'expectedPath' => DIRECTORY_SEPARATOR
                    . 'app' . DIRECTORY_SEPARATOR
                    . 'code' . DIRECTORY_SEPARATOR
                    . 'core' . DIRECTORY_SEPARATOR
                    . 'Mage' . DIRECTORY_SEPARATOR
                    . '*' . DIRECTORY_SEPARATOR
                    . 'etc' . DIRECTORY_SEPARATOR,
                'codePool' => 'core',
                'namespace' => 'Mage',
            ),
        );
    }

    public function testCreateNode()
    {
        $dom = new DOMDocument();
        $parent = $dom->createElement('parent');
        $parent->setAttribute('xpath', 'root');
        $dom->appendChild($parent);
        $nodeName = 'testNode';
        $newNode = $this->_model->createNode($dom, $nodeName, $parent);

        $this->assertEquals(1, $parent->childNodes->length);
        $this->assertEquals($newNode, $parent->childNodes->item(0));
        $this->assertEquals($nodeName, $newNode->getAttribute('id'));
        $this->assertEquals('root/testNode', $newNode->getAttribute('xpath'));
    }

    public function testSetMetaInfo()
    {
        $metaNodeName = array(
            'sort_order' => 'test_SortOrder',
            'title' => 'test_Title',
        );
        $this->_model->setMetaNodeNames($metaNodeName);

        $dom = new DOMDocument();
        $parent = $dom->createElement('parent');
        $parent->setAttribute('xpath', 'root');
        $parent->setAttribute('id', 'root_id');
        $dom->appendChild($parent);

        $dataNodeSortOrder = $dom->createElement('sort_order', '100');
        $dataNodeTitle = $dom->createElement('title', 'TestTitle');

        $this->_model->setMetaInfo($parent, $dataNodeSortOrder, 'Module_Name');
        $this->assertEmpty($this->_model->getAclResourceMaps());
        $this->_model->setMetaInfo($parent, $dataNodeTitle, 'Module_Name');

        $this->assertEquals(100, $parent->getAttribute('test_SortOrder'), 'Incorrect set of sort order');
        $this->assertEquals('TestTitle', $parent->getAttribute('test_Title'), 'Incorrect set of title');
        $this->assertEquals('Module_Name', $parent->getAttribute('module'), 'Incorrect set of module name');
        $maps = array('root' => 'Module_Name::root_id');
        $this->assertEquals($maps, $this->_model->getAclResourceMaps()); //test setting of id maps
    }

    public function testGetAdminhtmlFiles()
    {
        $this->_model->setAdminhtmlFiles(null);
        $this->assertEquals($this->_adminhtmlFiles,
            $this->_model->getAdminhtmlFiles(),
            'Incorrect file adminhtml file searching'
        );
    }

    public function testParseNode()
    {
        $dom = new DOMDocument();
        $dom->formatOutput = true;
        $parentNode = $dom->createElement('root');
        $dom->appendChild($parentNode);
        $moduleName = 'Module_Name';

        $sourceDom = new DOMDocument();
        $sourceDom->load($this->_fixturePath . DIRECTORY_SEPARATOR . 'parse_node_source.xml');
        $nodeList = $sourceDom->getElementsByTagName('resources');
        $this->_model->parseNode($nodeList->item(0), $dom, $parentNode, $moduleName);
        $expected = file_get_contents($this->_fixturePath . DIRECTORY_SEPARATOR . 'parse_node_result.xml');
        $this->assertEquals($expected, $dom->saveXML());
    }

    public function testGetResultDomDocument()
    {
        $dom = $this->_model->getResultDomDocument('Module_Name', 'Category');
        $expectedDom = new DOMDocument();
        $expectedDom->formatOutput = true;

        $file = $this->_fixturePath . DIRECTORY_SEPARATOR . 'template_document.xml';
        $expectedDom->load($file);
        $this->assertContains('{license_notice}', $dom->saveXML());
        $this->assertEquals($expectedDom->saveXML($expectedDom->documentElement), $dom->saveXML($dom->documentElement));
    }

    public function testParseAdminhtmlFiles()
    {
        $this->_model->parseAdminhtmlFiles();
        $this->assertCount(4, $this->_model->getParsedDomList());
        $this->assertCount(4, $this->_model->getAdminhtmlDomList());
    }

    /**
     * @covers Tools_Migration_Acl_Generator::updateAclResourceIds()
     * @covers Tools_Migration_Acl_Generator::updateChildAclNodes() (removing of xpath attribute)
     */
    public function testUpdateAclResourceIds()
    {
        $this->_model->parseAdminhtmlFiles();

        $domList = $this->_model->getParsedDomList();

        /** @var $dom DOMDocument **/
        foreach ($domList as $dom) {
            $xpath = new DOMXPath($dom);
            $resources = $xpath->query('//resources[@xpath]');
            $this->assertEquals(1, $resources->length);
        }
        $this->_model->updateAclResourceIds();
        /**
         * check that all xpath attributes are removed
         */
        /** @var $dom DOMDocument **/
        foreach ($domList as $dom) {
            $xpath = new DOMXPath($dom);
            $resources = $xpath->query('//*[@xpath]');
            $this->assertEquals(0, $resources->length);
        }
    }

    public function testUpdateChildAclNodes()
    {
        $dom = new DOMDocument();
        $fileActual = $this->_fixturePath . DIRECTORY_SEPARATOR . 'update_child_acl_nodes_source.xml';
        $fileExpected = $this->_fixturePath . DIRECTORY_SEPARATOR . 'update_child_acl_nodes_result.xml';
        $dom->load($fileActual);
        $rootNode = $dom->getElementsByTagName('resources')->item(0);

        $aclResourcesMaps = array(
            '/admin' => 'Map_Module::admin',
            '/admin/customer/manage' => 'Map_Module::manage',
            '/admin/system' => 'Map_Module::system',
            '/admin/system/config' => 'Map_Module::config',
        );

        $this->_model->setAclResourceMaps($aclResourcesMaps);
        $this->_model->updateChildAclNodes($rootNode);

        $expectedDom = new DOMDocument();
        $expectedDom->load($fileExpected);
        $expectedRootNode = $expectedDom->getElementsByTagName('resources')->item(0);

        $this->assertEquals($expectedDom->saveXML($expectedRootNode), $dom->saveXML($rootNode));
    }

    public function testIsNodeEmpty()
    {
        $dom = new DOMDocument();
        $node = $dom->createElement('node', 'test');
        $dom->appendChild($node);
        $this->assertTrue($this->_model->isNodeEmpty($node));

        $comment = $dom->createComment('comment');
        $node->appendChild($comment);
        $this->assertTrue($this->_model->isNodeEmpty($node));

        $subNode = $dom->createElement('subnode');
        $node->appendChild($subNode);
        $this->assertFalse($this->_model->isNodeEmpty($node));
    }
}
