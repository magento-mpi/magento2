<?php
/**
 * {license_notice}
 *
 * @category   Mage
 * @package    Mage
 * @copyright  {copyright}
 * @license    {license_link}
 */

define('USAGE', <<<USAGE
$>./acl.php -- [-dseh]
    additional parameters:
    -h          print usage
    -p          preview result
USAGE
);
require_once ( __DIR__ . '/Menu/Generator.php');

class Tools_Migration_Acl_Generator
{
    /**
     * @var bool
     */
    protected $_printHelp = false;

    /**
     * Meta node names
     *
     * @var array
     */
    protected $_metaNodeNames = array();

    /**
     * Restricted node names
     *
     * @var array
     */
    protected $_restrictedNodeNames = array();

    /**
     * Adminhtml files
     *
     * @var array|null
     */
    protected $_adminhtmlFiles = null;

    /**
     * Menu files
     *
     * @var array|null
     */
    protected $_menuFiles = null;

    /**
     * Valid node types
     *
     * @var array
     */
    protected $_validNodeTypes = array();

    /**
     * Parsed dom list
     *
     * @var array
     */
    protected $_parsedDomList = array();

    /**
     * Map ACL resource xpath to id
     * @var array
     */
    protected $_aclResourceMaps = array();

    /**
     * Map Menu ids
     *
     * @var array
     */
    protected $_menuIdMaps = array();

    /**
     * Base application path
     *
     * @var string|null
     */
    protected $_basePath = null;

    /**
     * Nodes that needed to be removed
     *
     * @var array
     */
    protected $_nodeToRemove = array();

    /**
     * Adminhtml DOMDocument list
     *
     * @var array
     */
    protected $_adminhtmlDomList = array();

    /**
     * @var string
     */
    protected $_artifactsPath;

    /**
     * Is preview mode
     *
     * @var bool
     */
    protected $_isPreviewMode = false;

    /**
     * Default constructor
     */
    public function __construct($options = array())
    {
        if (false == function_exists('tidy_parse_string')) {
            throw new Exception('Error! php_tidy extension is required');
        }
        $this->_printHelp = array_key_exists('h', $options);
        $this->_isPreviewMode = array_key_exists('p', $options);

        $this->_metaNodeNames = array(
            'sort_order' => 'sortOrder',
            'title' => 'title',
        );

        $this->_restrictedNodeNames = array(
            'children',
        );

        $this->_validNodeTypes = array(
            1,  //DOMElement
        );

        $this->_basePath = realpath(dirname(__FILE__) . '/../../../..');

        $this->_artifactsPath = realpath(dirname(__FILE__)) . DIRECTORY_SEPARATOR . 'log' . DIRECTORY_SEPARATOR;

        $this->_nodeToRemove = array(
            'resources',
            'privilegeSets',
        );
    }

    /**
     * Get Comment text
     *
     * @param $category
     * @param $package
     * @return string
     */
    public function getCommentText($category, $package)
    {
        $comment = PHP_EOL;
        $comment .= '/**' . PHP_EOL;
        $comment .= '* {license_notice}' . PHP_EOL;
        $comment .= '*' . PHP_EOL;
        $comment .= '* @category    ' . $category . PHP_EOL;
        $comment .= '* @package     ' . $package . PHP_EOL;
        $comment .= '* @copyright   {copyright}' . PHP_EOL;
        $comment .= '* @license     {license_link}' . PHP_EOL;
        $comment .= '*/' . PHP_EOL;

        return $comment;
    }

    /**
     * Get module name from file name
     *
     * @param $fileName
     * @return string
     */
    public function getModuleName($fileName)
    {
        $parts = array_reverse(explode(DIRECTORY_SEPARATOR, $fileName));
        $module = $parts[3] . '_' . $parts[2];
        return $module;
    }

    /**
     * Get category name from file name
     *
     * @param $fileName
     * @return string
     */
    public function getCategory($fileName)
    {
        $parts = array_reverse(explode(DIRECTORY_SEPARATOR, $fileName));
        return $parts[3];
    }

    /**
     * Get is restricted node
     *
     * @param string $nodeName
     * @return bool
     */
    public function isRestrictedNode($nodeName)
    {
        return in_array($nodeName, $this->getRestrictedNodeNames());
    }

    /**
     * Get is meta-info node
     *
     * @param string $nodeName
     * @return bool
     */
    public function isMetaNode($nodeName)
    {
        return isset($this->_metaNodeNames[$nodeName]);
    }

    /**
     * @param array $restrictedNodeNames
     */
    public function setRestrictedNodeNames($restrictedNodeNames)
    {
        $this->_restrictedNodeNames = $restrictedNodeNames;
    }

    /**
     * @return array
     */
    public function getRestrictedNodeNames()
    {
        return $this->_restrictedNodeNames;
    }

    /**
     * @param array $metaNodeNames
     */
    public function setMetaNodeNames($metaNodeNames)
    {
        $this->_metaNodeNames = $metaNodeNames;
    }

    /**
     * @return array
     */
    public function getMetaNodeNames()
    {
        return $this->_metaNodeNames;
    }

    /**
     * Get is valid node type
     *
     * @param int $nodeType
     * @return bool
     */
    public function isValidNodeType($nodeType)
    {
        return in_array($nodeType, $this->_validNodeTypes);
    }

    /**
     * Get etc directory pattern
     *
     * @param string $codePool
     * @param string $namespace
     * @return null|string
     */
    public function getEtcDirPattern($codePool = '*', $namespace = '*')
    {
        return $this->getBasePath() . DIRECTORY_SEPARATOR
            . 'app' . DIRECTORY_SEPARATOR
            . 'code' . DIRECTORY_SEPARATOR
            . $codePool . DIRECTORY_SEPARATOR //code pool
            . $namespace . DIRECTORY_SEPARATOR //namespace
            . '*' . DIRECTORY_SEPARATOR //module name
            . 'etc' . DIRECTORY_SEPARATOR;
    }

    /**
     * @param null|string $basePath
     */
    public function setBasePath($basePath)
    {
        $this->_basePath = $basePath;
    }

    /**
     * @return null|string
     */
    public function getBasePath()
    {
        return $this->_basePath;
    }

    /**
     * Create node
     *
     * @param DOMDocument $resultDom
     * @param string $nodeName
     * @param DOMNode $parent
     * @return DOMNode
     */
    public function createNode(DOMDocument $resultDom, $nodeName, DOMNode $parent)
    {
        $newNode = $resultDom->createElement('resource');
        $newNode->setAttribute('id', $nodeName);
        $newNode->setAttribute('xpath', $parent->getAttribute('xpath') . '/' . $nodeName);
        $parent->appendChild($newNode);
        return $newNode;
    }

    /**
     * Set meta node
     *
     * @param DOMNode $node
     * @param DOMNode $dataNode
     * @param string $module
     */
    public function setMetaInfo(DOMNode $node, DOMNode $dataNode, $module)
    {
        $node->setAttribute($this->_metaNodeNames[$dataNode->nodeName], $dataNode->nodeValue);
        if ($dataNode->nodeName == 'title') {
            $node->setAttribute('module', $module);
            $id = $node->getAttribute('module') . '::' . $node->getAttribute('id');
            $this->_aclResourceMaps[$node->getAttribute('xpath')] = $id;
        }
    }

    /**
     * @return array
     */
    public function getAclResourceMaps()
    {
        return $this->_aclResourceMaps;
    }

    /**
     * @return array
     */
    public function getAdminhtmlFiles()
    {
        if (null === $this->_adminhtmlFiles) {
            $localFiles = glob($this->getEtcDirPattern('local') . 'adminhtml.xml');
            $communityFiles = glob($this->getEtcDirPattern('community') . 'adminhtml.xml');
            $coreEnterpriseFiles = glob($this->getEtcDirPattern('core', 'Enterprise') . 'adminhtml.xml');
            $coreMageFiles = glob($this->getEtcDirPattern('core', 'Mage') . 'adminhtml.xml');
            $this->_adminhtmlFiles = array_merge($localFiles, $communityFiles, $coreEnterpriseFiles, $coreMageFiles);
        }
        return $this->_adminhtmlFiles;
    }

    /**
     * @param array|null $adminhtmlFiles
     */
    public function setAdminhtmlFiles($adminhtmlFiles)
    {
        $this->_adminhtmlFiles = $adminhtmlFiles;
    }

    /**
     * @return array
     */
    public function getParsedDomList()
    {
        return $this->_parsedDomList;
    }

    /**
     * Parse node
     *
     * @param DOMNode $node - data source
     * @param DOMDocument $dom - result DOMDocument
     * @param DOMNode $parentNode - parent node from result document
     * @param $moduleName
     */
    public function parseNode(DOMNode $node, DOMDocument $dom, DOMNode $parentNode, $moduleName)
    {
        foreach ($node->childNodes as $item) {
            if (false == $this->isValidNodeType($item->nodeType)) {
                continue;
            }
            if ($this->isRestrictedNode($item->nodeName)) {
                $this->parseNode($item, $dom, $parentNode, $moduleName);
            } elseif ($this->isMetaNode($item->nodeName)) {
                $this->setMetaInfo($parentNode, $item, $moduleName);
            } else {
                $newNode = $this->createNode($dom, $item->nodeName, $parentNode);
                if ($item->childNodes->length > 0) {
                    $this->parseNode($item, $dom, $newNode, $moduleName);
                }
            }
        }
    }

    /**
     * Print help message
     */
    public function printHelpMessage()
    {
        echo USAGE;
    }

    /**
     * Get template for result DOMDocument
     * @param $module
     * @param $category
     * @return DOMDocument
     */
    public function getResultDomDocument($module, $category)
    {
        $resultDom = new DOMDocument();
        $resultDom->formatOutput = true;

        $comment = $resultDom->createComment($this->getCommentText($category, $module));
        $resultDom->appendChild($comment);

        $config = $resultDom->createElement('config');
        $resultDom->appendChild($config);

        $acl = $resultDom->createElement('acl');
        $config->appendChild($acl);

        $parent = $resultDom->createElement('resources');
        $parent->setAttribute('xpath', 'config/acl/resources');
        $acl->appendChild($parent);
        return $resultDom;
    }

    /**
     * Parse adminhtml.xml files
     */
    public function parseAdminhtmlFiles()
    {
        foreach ($this->getAdminhtmlFiles() as $file) {
            $module = $this->getModuleName($file);
            $category = $this->getCategory($file);
            $resultDom = $this->getResultDomDocument($module, $category);

            $adminhtmlDom = new DOMDocument();
            $adminhtmlDom->load($file);
            $this->_adminhtmlDomList[$file] = $adminhtmlDom;

            $xpath = new DOMXPath($adminhtmlDom);
            $resourcesList = $xpath->query('//config/acl/*');
            /** @var $aclNode DOMNode **/
            foreach ($resourcesList as $aclNode) {
                $this->parseNode($aclNode, $resultDom, $resultDom->getElementsByTagName('resources')->item(0), $module);
            }
            $this->_parsedDomList[$file] = $resultDom;

        }
    }

    /**
     * Update ACL resource id
     */
    public function updateAclResourceIds()
    {
        /**  @var $dom DOMDocument **/
        foreach ($this->_parsedDomList as $dom) {
            $list = $dom->getElementsByTagName('resources');
            /** @var $node DOMNode **/
            foreach ($list as $node) {
                $node->removeAttribute('xpath');
                if ($node->childNodes->length > 0) {
                    $this->updateChildAclNodes($node);
                }
            }
        }
    }

    /**
     * @param $node DOMNode
     */
    public function updateChildAclNodes($node)
    {
        /** @var $item DOMNode **/
        foreach ($node->childNodes as $item) {
            if (false == $this->isValidNodeType($item->nodeType)) {
                continue;
            }
            $xpath = $item->getAttribute('xpath');
            $id = $item->getAttribute('module') . '::' . $item->getAttribute('id');
            if (isset($this->_aclResourceMaps[$xpath])) {
                $id = $this->_aclResourceMaps[$xpath];
            }
            $item->setAttribute('id', $id);
            $item->removeAttribute('xpath');
            $item->removeAttribute('module');

            if ($item->childNodes->length > 0) {
                $this->updateChildAclNodes($item);
            }
        }
    }

    /**
     * @param array $aclResourceMaps
     */
    public function setAclResourceMaps($aclResourceMaps)
    {
        $this->_aclResourceMaps = $aclResourceMaps;
    }

    /**
     * Save ACL files
     */
    public function saveAclFiles()
    {
        /** @var $dom DOMDocument **/
        foreach ($this->_parsedDomList as $originFile => $dom) {
            $file = str_replace('adminhtml.xml', 'adminhtml' . DIRECTORY_SEPARATOR . 'acl.xml', $originFile);
            $directory = dirname($file);
            if (false == is_dir($directory)) {
                if (false == $this->_isPreviewMode) {
                    mkdir($directory, 0777, true);
                }
            }
            if (false == $this->_isPreviewMode) {
                $dom->preserveWhiteSpace = false;
                $dom->formatOutput = true;
                $tidy = tidy_parse_string($dom->saveXml(), array(
                    'indent' => true,
                    'input-xml' => true,
                    'output-xml' => true,
                    'add-xml-space' => false,
                    'indent-spaces' => 4,
                    'wrap' => 1000
                ));
                file_put_contents($file, $tidy->value);
            }
        }
    }

    /**
     * @param array $parsedDomList
     */
    public function setParsedDomList($parsedDomList)
    {
        $this->_parsedDomList = $parsedDomList;
    }

    /**
     * @param array $adminhtmlDomList
     */
    public function setAdminhtmlDomList($adminhtmlDomList)
    {
        $this->_adminhtmlDomList = $adminhtmlDomList;
    }

    /**
     * @return array
     */
    public function getAdminhtmlDomList()
    {
        return $this->_adminhtmlDomList;
    }

    /**
     * Remove empty files
     */
    public function removeAdminhtmlFiles()
    {
        $output = array(
            'removed' => array(),
            'not_removed' => array(),
        );

        /** @var $dom DOMDocument **/
        foreach ($this->_adminhtmlDomList as $file => $dom) {
            $xpath = new DOMXpath($dom);
            $nodeList = $xpath->query('/config/acl');
            if ($nodeList->length == 0) {
                continue;
            }
            $acl = $nodeList->item(0);
            $countNodes = $acl->childNodes->length - 1;
            for ($i = $countNodes; $i >= 0 ; $i--) {
                $node = $acl->childNodes->item($i);
                if (in_array($node->nodeName, $this->_nodeToRemove)) {
                    $acl->removeChild($node);
                }
            }
            if ($this->isNodeEmpty($acl)) {
                if (false == $this->_isPreviewMode) {
                    unlink($file);
                }
                $output['removed'][] = $file;
            } else {
                $output['not_removed'][] = $file;
            }
        }

        $output['artifacts']['AclXPathToAclId.log'] = json_encode($this->_aclResourceMaps);
        return $output;
    }

    /**
     * Check if node is empty
     *
     * @param DOMNode $node
     * @return bool
     */
    public function isNodeEmpty(DOMNode $node)
    {
        $output = true;
        foreach ($node->childNodes as $item) {
            if ($this->isValidNodeType($item->nodeType)) {
                $output = false;
            }
        }
        return $output;
    }

    /**
     * @param string $xpathToIdFile
     */
    public function setArtifactsPath($xpathToIdFile)
    {
        $this->_artifactsPath = $xpathToIdFile;
    }

    /**
     * Run migration process
     */
    public function run()
    {
        if ($this->_printHelp) {
            $this->printHelpMessage();
            return;
        }
        $this->parseAdminhtmlFiles();

        $this->updateAclResourceIds();

        $this->saveAclFiles();

        $result = $this->removeAdminhtmlFiles();

        $menuResult = $this->processMenu();

        $artifacts = array_merge($result['artifacts'], $menuResult['artifacts']);

        $this->saveArtifacts($artifacts);

        $this->printStatistic($result, $menuResult, $artifacts);
    }

    /**
     * Print statistic
     *
     * @param $result
     * @param $menuResult
     * @param $artifacts
     */
    public function printStatistic($result, $menuResult, $artifacts)
    {
        $output = PHP_EOL;
        if (true == $this->_isPreviewMode) {
            $output .= '!!! PREVIEW MODE. ORIGIN DATA NOT CHANGED!!!' . PHP_EOL;
        }

        $output .= PHP_EOL;

        $output .= 'Removed adminhtml.xml: ' . count($result['removed']) . ' files ' . PHP_EOL;
        $output .= 'Not Removed adminhtml.xml: ' . count($result['not_removed']) . ' files ' . PHP_EOL;
        if (count($result['not_removed'])) {
            foreach ($result['not_removed'] as $fileName) {
                $output .= ' - ' . $fileName . PHP_EOL;
            }
        }

        $output .= PHP_EOL;
        $output .= 'Mapped Menu Items: ' . count($menuResult['mapped']) . PHP_EOL;
        $output .= 'Not Mapped Menu Items: ' .count($menuResult['not_mapped']) . PHP_EOL;

        if (count($menuResult['not_mapped'])) {
            foreach ($menuResult['not_mapped'] as $menuId) {
                $output .= ' - ' . $menuId . PHP_EOL;
            }
        }

        $output .= 'Menu Update Errors: ' .count($menuResult['menu_update_errors']) . PHP_EOL;
        if (count($menuResult['menu_update_errors'])) {
            foreach ($menuResult['menu_update_errors'] as $errorText) {
                $output .= ' - ' . $errorText . PHP_EOL;
            }
        }

        $output .= PHP_EOL;
        $output .= 'Artifacts: ' . PHP_EOL;
        foreach ($artifacts as $file => $data) {
            $output .= ' - ' . $this->_artifactsPath . $file . PHP_EOL;
        }

        echo $output;
    }

    /**
     * Save artifacts files
     *
     * @param $artifacts
     */
    public function saveArtifacts($artifacts)
    {
        if (false == is_dir($this->_artifactsPath)) {
            mkdir($this->_artifactsPath, 0777, true);
        }
        foreach ($artifacts as $file => $data) {
            file_put_contents($this->_artifactsPath . $file, $data);
        }
    }

    /**
     * Run process of menu updating
     *
     * @return array
     */
    public function processMenu()
    {
        $menu = new Tools_Migration_Acl_Menu_Generator(
            $this->getBasePath(),
            $this->_validNodeTypes,
            $this->_aclResourceMaps,
            $this->_isPreviewMode
        );
        return $menu->run();
    }
}
