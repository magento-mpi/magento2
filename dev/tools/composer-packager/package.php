<?php

define('BP', dirname(__DIR__ . '/../../../..'));

static $modules = array();
static $themes = array();

$modulePath = BP . '/app/code/Magento/';
$adminThemePath = BP.'/app/design/adminhtml/Magento/';
$frontendThemePath = BP.'/app/design/frontend/Magento/';

function autoload($className)
{
    $className = ltrim($className, '\\');
    $fileName  = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require $fileName;
}
spl_autoload_register('autoload');


function createModule($moduleDefinition, &$mods)
{
    $moduleName = convertModuleNametoVendorPackage($moduleDefinition->name);
    echo "Creating Module " . $moduleName . "\n";
    $module = null;
    //Check if module already exists.
    if (array_key_exists($moduleName, $mods)) {
       // echo "Module $moduleName already exists \n";
        //Add version and other info to it.
        $module = $mods[$moduleName];
        $module->setVersion($moduleDefinition->version);
        $module->setActive($moduleDefinition->active);
        $module->setLocation($moduleDefinition->location);

    } else {
       // echo "Creating new Module Object for $moduleName \n";
        $module = new Magento\Composer\Model\Module($moduleName, $moduleDefinition->version, $moduleDefinition->active, $moduleDefinition->location);
        $mods[$moduleName] = $module;
    }
  //  echo "Printing Dependencies: \n";
    foreach ($moduleDefinition->dependencies as $dependency) {
        $dependentModule = convertModuleNametoVendorPackage($dependency);
        //echo "       Dependency : $dependentModule \n";
        //Check if Module instance already exists.
        if (array_key_exists($dependentModule, $mods)) {
            //echo "       Found the existing Module : $dependentModule \n";
            //Already exists.
            $depModule = $mods[$dependentModule];
        } else {
           // echo "       Creating a new Dependency Module : $dependentModule \n";
            //Make a new one
            $depModule = new Magento\Composer\Model\Module($dependentModule);
            $mods[$dependentModule] = $depModule;
        }
        $module->addDependencies($depModule);
    }
    return $module;
}

function createTheme($themeDefinitions, &$lstthemes){
    $themeName = convertModuleNametoVendorPackage($themeDefinitions->name);
    echo "Creating Theme " . $themeName . "\n";
    $theme = null;
    if(array_key_exists($themeName, $lstthemes)){
        //Add version and other info to it.
        $theme = $lstthemes[$themeName];
        $theme->setVersion($themeDefinitions->version);
        $theme->setLocation($themeDefinitions->location);
    } else {
        //create a new one
        $theme = new Magento\Composer\Model\Theme($themeName, $themeDefinitions->version, $themeDefinitions->location);
        $lstthemes[$themeName] = $theme;
    }
    if(isset($themeDefinitions->dependencies)){
        //Check for Dependencies
        foreach ($themeDefinitions->dependencies as $dependency) {
            echo "       Dependency : $dependency \n";

            //Check if Module instance already exists.
            if (array_key_exists($dependency, $lstthemes)) {
                echo "       Found the existing Module : $dependency \n";
                //Already exists.
                $depTheme = $lstthemes[$dependency];
            } else {
                 echo "       Creating a new Dependency Module : $dependency \n";
                //Make a new one
                $depTheme = new Magento\Composer\Model\Theme($dependency);
                $lstthemes[$dependency] = $depTheme;
            }
            $theme->addDependencies($depTheme);
        }
    }
    return $theme;
}

function convertModuleNametoVendorPackage($name)
{
    if ($name != null && sizeof($name) > 0 && strpos($name, "_") != false) {
        //return strtolower(str_replace("_", DIRECTORY_SEPARATOR, $name));
        return preg_replace("/_/", DIRECTORY_SEPARATOR, $name, 1);
    }
}

function convertVendorPackagetoModuleName($vendorPackage){
    if($vendorPackage != null && sizeof($vendorPackage) > 0 && strpos($vendorPackage, DIRECTORY_SEPARATOR) != false){
        return str_replace(DIRECTORY_SEPARATOR, "_" , $vendorPackage);
    }
}

function createThemePackage($themes){
    echo "\n\nCreating Composer JSON : \n";
    foreach($themes as $theme){
        echo $theme->getName() . "\n";
        $command = "cd ".$theme->getLocation() ." && php ".__DIR__."/composer.phar init  --name \"". $theme->getName(). "\" --description=\"This is the description\" --author=\"Jay Patel <jaypatel512@gmail.com>\" --stability=\"dev\" -n";
        //Command to include package installer.
        $dependencies = $theme->getDependencies();
        foreach($dependencies as $dependency){
            $command .= " --require=\"" . $dependency->getName().":".$dependency->getVersion()."\" ";
        }
        $command .= " --require=\"magento/package-installer:*\"";
        //        echo $command, "\n";
        $output = array();
        exec($command, $output);
        if(sizeof($output) > 0 ){
            print_r($output);
        }
    }
}

function createComposerPackage($modules){
    echo "\n\nCreating Composer JSON : ", "\n";
    foreach($modules as $module){
        echo $module->getName() . "\n";
        $command = "cd ".$module->getLocation() ." && php ".__DIR__."/composer.phar init  --name \"". $module->getName(). "\" --description=\"This is the description\" --author=\"Jay Patel <jaypatel512@gmail.com>\" --stability=\"dev\" -n";
        // We are getting the iterator of the object
        $dependencies = $module->getDependencies();
        foreach($dependencies as $dependency){
            $command .= " --require=\"" . $dependency->getName().":".$dependency->getVersion()."\" ";
        }
        //Command to include package installer.
        $command .= " --require=\"magento/package-installer:*\"";
        //        echo $command, "\n";
        $output = array();
        exec($command, $output);
        if(sizeof($output) > 0 ){
            print_r($output);
        }

    }
}


function zipComposerPackage($modules){
    echo "\n\nZipping module : \n";
    if (!file_exists('packages')) {
    	mkdir('packages', 0777, true);
	}
    foreach($modules as $module){

        //We need to update version number if we are using artifact files for our stuffs.
        updateModuleVersionInfo($module);

        echo $module->getName(), "\n";
        Magento\Composer\Helper\Zip::Zip($module->getLocation(), "packages/".convertVendorPackagetoModuleName($module->getName()). "-". $module->getVersion() . ".zip");

    }
}

function zipThemeComposerPackages($themes){
    echo "\n\nZipping themes : \n";
    foreach($themes as $theme){

        //We need to update version number if we are using artifact files for our stuffs.
        updatThemeVersionInfo($theme);

        echo $theme->getName(), "\n";
        Magento\Composer\Helper\Zip::Zip(realpath($theme->getLocation()), "packages/".convertVendorPackagetoModuleName($theme->getName()). "-". $theme->getVersion() . ".zip");

    }
}

function updatThemeVersionInfo($theme){
    if($theme->getVersion() != null && $theme->getVersion() != ""){
        $json = file_get_contents($theme->getLocation()."/composer.json");
        $themeComposer = json_decode($json, true);
        if(!array_key_exists("type", $themeComposer)){
            $themeComposer["type"] = "magento2-module";
        }
        if(!array_key_exists("version", $themeComposer)){
            $themeComposer["version"] = $theme->getVersion();
        }
        file_put_contents($theme->getLocation()."/composer.json", json_encode($themeComposer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}

function updateModuleVersionInfo($module){
    if($module->getVersion() != null && $module->getVersion() != ""){
        $json = file_get_contents($module->getLocation()."/composer.json");
        $moduleComposer = json_decode($json, true);
        if(!array_key_exists("type", $moduleComposer)){
            $moduleComposer["type"] = "magento2-module";
        }
        if(!array_key_exists("version", $moduleComposer)){
            $moduleComposer["version"] = $module->getVersion();
        }
        file_put_contents($module->getLocation()."/composer.json", json_encode($moduleComposer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}



/*
foreach (new DirectoryIterator($modulePath) as $module) {
    if ($module->isDot()) continue;

    if ($module->isDir()) {
        $parser = new Magento\Composer\Parser\PackageXmlParser($modulePath . $module->getFilename() );
        $moduleDefinition = $parser->getMappings();
        $module = createModule($moduleDefinition, $modules);
    }
}
*/
foreach (new DirectoryIterator($adminThemePath) as $theme) {
    if ($theme->isDot()) continue;

    if ($theme->isDir()) {
        $parser = new Magento\Composer\Parser\ThemeXmlParser($adminThemePath . $theme->getFilename() );
        $themeDefinitions = $parser->getMappings();
        $theme = createTheme($themeDefinitions, $themes);
    }
}
foreach (new DirectoryIterator($frontendThemePath) as $theme) {
    if ($theme->isDot()) continue;

    if ($theme->isDir()) {
        $parser = new Magento\Composer\Parser\ThemeXmlParser($frontendThemePath . $theme->getFilename() );
        $themeDefinitions = $parser->getMappings();
        $theme = createTheme($themeDefinitions, $themes);
    }
}

//createComposerPackage($modules);
createThemePackage($themes);
//zip each modules in here.
//zipComposerPackage($modules);
//zip each modules in here.
zipThemeComposerPackages($themes);

echo "\n\n\n COMPLETED PACKAGING. You should be able to find packages on /dev/composer-packager/packages/ \n";

