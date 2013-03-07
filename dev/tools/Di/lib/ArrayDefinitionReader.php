<?php
/**
 * Codebase constructor signature reader
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class ArrayDefinitionReader
{
    /**
     * Compile definitions using Magento_Di_Definition_CompilerDefinition_Zend
     *
     * @param string $moduleDir
     * @return array
     */
    public function compileModule($moduleDir)
    {
        $strategy = new \Zend\Di\Definition\IntrospectionStrategy(new \Zend\Code\Annotation\AnnotationManager());
        $strategy->setMethodNameInclusionPatterns(array());
        $strategy->setInterfaceInjectionInclusionPatterns(array());

        $compiler = new Magento_Di_Definition_CompilerDefinition_Zend($strategy);
        $compiler->addDirectory($moduleDir);

        $controllerPath = $moduleDir . '/controllers/';
        if (file_exists($controllerPath)) {
            /** @var $file DirectoryIterator */
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($controllerPath)) as $file) {
                if (!$file->isDir()) {
                    require_once $file->getPathname();
                }
            }
        }

        $compiler->compile();
        return $compiler->toArray();
    }
}
