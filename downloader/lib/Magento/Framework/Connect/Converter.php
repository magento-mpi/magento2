<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Connect;

/**
 * Class for convertiong old magento PEAR packages to new one
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
final class Converter
{
    /**
     * @var \Magento\Framework\Archive
     */
    protected $_archiver;

    /**
     *
     * @return \Magento\Framework\Archive
     */
    public function arc()
    {
        if (!$this->_archiver) {
            $this->_archiver = new \Magento\Framework\Archive();
        }
        return $this->_archiver;
    }

    /**
     * @return Package
     */
    public function newPackage()
    {
        return new \Magento\Framework\Connect\Package();
    }

    /**
     *
     * @return Pear_Package_Parser_v2
     */
    public function oldPackageReader()
    {
        return new Pear_Package_Parser_v2();
    }

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @param string $channel
     * @return string
     */
    public function convertChannelName($channel)
    {
        return str_replace("connect.magentocommerce.com/", "", $channel);
    }

    /**
     * Convert package dependencies - urls - by ref
     * @param array $oldDeps  ref to array
     * @return array
     */
    public function convertPackageDependencies($oldDeps)
    {
        $out = array();
        if (empty($oldDeps['required']['package'])) {
            return $out;
        }
        $deps = $oldDeps['required']['package'];
        if (!isset($deps[0])) {
            $deps = array($deps);
        }
        for ($i = 0,$c = count($deps); $i < $c; $i++) {
            $deps[$i]['min_version'] = isset($deps[$i]['min']) ? $deps[$i]['min'] : false;
            $deps[$i]['max_version'] = isset($deps[$i]['max']) ? $deps[$i]['max'] : false;
            $deps[$i]['channel'] = $this->convertChannelName($deps[$i]['channel']);
            $out[] = $deps[$i];
        }

        return $out;
    }

    /**
     * @param array $oldLicense
     * @return array|bool|float|int|string
     */
    public function convertLicense($oldLicense)
    {
        if (is_scalar($oldLicense)) {
            return $oldLicense;
        }
        return array($oldLicense['_content'], $oldLicense['attribs']['uri']);
    }

    /**
     * @param array $maintainers
     * @return array
     */
    public function convertMaintainers($maintainers)
    {
        if (!is_array($maintainers) || !count($maintainers)) {
            return array();
        }
        $out = array();
        foreach ($maintainers as $row) {
            $out[] = array('name' => $row['name'], 'email' => $row['email'], 'user' => 'auto-converted');
        }
        return $out;
    }

    /**
     * @var array
     */
    protected $fileMap = array();

    /**
     * Conver pear package object to magento object
     * @param Pear_Package_V2 $pearObject
     * @return \Magento\Framework\Connect\Package
     */
    public function convertPackageObject($pearObject)
    {
        $data = array();
        $mageObject = $this->newPackage();



        $map = array(
            'name' => null,
            'version' => array('getterArgs' => array('release')),
            'package_deps' => array(
                'getter' => 'getDependencies',
                'converter' => 'convertPackageDependencies',
                'setter' => 'setDependencyPackages'
            ),
            'stability' => array('getter' => 'getState', 'getterArgs' => array('release')),
            'license' => array('getterArgs' => array(true), 'converter' => 'convertLicense', 'noArrayWrap' => true),
            'summary' => null,
            'description' => null,
            'notes' => null,
            'date' => null,
            'time' => null,
            'authors' => array('converter' => 'convertMaintainers', 'getter' => 'getMaintainers'),
            'channel' => array('converter' => 'convertChannelName')
        );
        foreach ($map as $field => $rules) {

            if (empty($rules)) {
                $rules = array('setter' => '', 'getter' => '');
            }

            if (empty($rules['getter'])) {
                $rules['getter'] = 'get' . ucfirst($field);
            }

            $useSetter = empty($rules['noSetter']);
            $useGetter = empty($rules['noGetter']);


            if (empty($rules['setter'])) {
                $rules['setter'] = 'set' . ucfirst($field);
            }
            if (empty($rules['getterArgs'])) {
                $rules['getterArgs'] = array();
            } elseif (!is_array($rules['getterArgs'])) {
                throw new \Exception("Invalid 'getterArgs' for '{$field}', should be array");
            }

            if ($useGetter
                && (!method_exists($pearObject, $rules['getter'])
                    || !is_callable([$pearObject, $rules['getter']])
                )
            ) {
                $mName = get_class($pearObject) . "::" . $rules['getter'];
                throw new \Exception('No getter method exists: ' . $mName);
            }

            if ($useSetter
                && (!method_exists($mageObject, $rules['setter'])
                    || !is_callable([$mageObject, $rules['setter']])
                )
            ) {
                $mName = get_class($mageObject) . "::" . $rules['setter'];
                throw new \Exception('No setter method exists: ' . $mName);
            }

            $useConverter = !empty($rules['converter']);

            if ($useConverter && !method_exists($this, $rules['converter'])) {
                $mName = get_class($this) . "::" . $rules['converter'];
                throw new \Exception('No converter method exists: ' . $mName);
            }

            if ($useGetter) {
                $getData = call_user_func_array(array($pearObject, $rules['getter']), $rules['getterArgs']);
            } else {
                $getData = array();
            }

            if ($useConverter) {
                $args = array();
                if (!$useGetter && !$useSetter) {
                    $args = array($pearObject, $mageObject);
                } elseif (!$useSetter) {
                    $args = array($mageObject, $getData);
                } else {
                    $args = array($getData);
                }
                $getData = call_user_func_array(array($this, $rules['converter']), $args);
            }

            $noWrap = !empty($rules['noArrayWrap']);
            if ($useSetter) {
                $setData = call_user_func_array(
                    array($mageObject, $rules['setter']),
                    $noWrap ? $getData : array($getData)
                );
            }
        }
        return $mageObject;
    }

    /**
     * Convert PEAR package to Magento package
     * @param string $sourceFile  path to PEAR .tgz
     * @param string|false $destFile    path to newly-created Magento .tgz, false to specify auto
     * @return bool
     */
    public function convertPearToMage($sourceFile, $destFile = false)
    {
        try {
            if (!file_exists($sourceFile)) {
                throw new \Exception("File doesn't exist: {$sourceFile}");
            }
            $arc = $this->arc();
            $tempDir = "tmp-" . basename($sourceFile) . uniqid();
            $outDir = "out-" . basename($sourceFile) . uniqid();
            $outDir = rtrim($outDir, "\\/");
            \Magento\Framework\System\Dirs::mkdirStrict($outDir);
            \Magento\Framework\System\Dirs::mkdirStrict($tempDir);

            $result = $arc->unpack($sourceFile, $tempDir);
            if (!$result) {
                throw new \Exception("'{$sourceFile}' was not unpacked");
            }

            $result = rtrim($result, "\\/");
            $packageXml = $result . '/package.xml';
            if (!file_exists($packageXml)) {
                throw new \Exception("No package.xml found inside '{$sourceFile}'");
            }

            $reader = $this->oldPackageReader();
            $data = file_get_contents($packageXml);

            $pearObject = $reader->parsePackage($data, $packageXml);
            $mageObject = $this->convertPackageObject($pearObject);
            if (!$mageObject->validate()) {
                throw new \Exception("Package validation failed.\n" . implode("\n", $mageObject->getErrors()));
            }

            /**
             * Calculate destination file if false
             */
            if (false === $destFile) {
                $pathinfo = pathinfo($sourceFile);
                $destFile = $pathinfo['dirname'] . '/' . $pathinfo['filename'] . '-converted';
                if (isset($pathinfo['extension'])) {
                    $destFile .= "." . $pathinfo['extension'];
                }
            }

            $target = new \Magento\Framework\Connect\Package\Target("target.xml");
            $targets = $target->getTargets();
            $mageObject->setTarget($target);
            $validRoles = array_keys($targets);
            $data = $pearObject->getFilelist();
            $pathSource = dirname(
                $pearObject->getPackageFile()
            ) . '/' . $pearObject->getName() . "-" . $pearObject->getVersion();

            $filesToDo = array();
            foreach ($data as $file => $row) {
                $name = $row['name'];
                $role = $row['role'];
                if (!in_array($role, $validRoles)) {
                    $role = 'mage';
                }
                $baseName = ltrim($targets[$role], "\\/.");
                $baseName = rtrim($baseName, "\\/");
                $sourceFile = $pathSource . '/' . $name;
                $targetFile = $outDir . '/' . $baseName . '/' . $name;
                if (file_exists($sourceFile)) {
                    \Magento\Framework\System\Dirs::mkdirStrict(dirname($targetFile));
                    $copy = @copy($sourceFile, $targetFile);
                    if (false === $copy) {
                        throw new \Exception("Cannot copy '{$sourceFile}' to '{$targetFile}'");
                    }
                }
                $filesToDo[] = array('name' => $name, 'role' => $role);
            }
            $cwd = getcwd();
            @chdir($outDir);
            foreach ($filesToDo as $fileToDo) {
                $mageObject->addContent($fileToDo['name'], $fileToDo['role']);
            }
            $mageObject->save(getcwd());
            @chdir($cwd);
            $filename = $outDir . '/' . $mageObject->getReleaseFilename() . ".tgz";
            if (@file_exists($destFile)) {
                @unlink($destFile);
            }
            \Magento\Framework\System\Dirs::mkdirStrict(dirname($destFile));
            $copy = @copy($filename, $destFile);
            if (false === $copy) {
                throw new \Exception("Cannot copy '{$filename}' to '{$destFile}'");
            }
            \Magento\Framework\System\Dirs::rm($tempDir);
            \Magento\Framework\System\Dirs::rm($outDir);
        } catch (\Exception $e) {
            throw $e;
        }
        return $destFile;
    }
}
