<?php
/**
 * Multi-tenant deployment code base service model
 *
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
namespace Magento\MultiTenant;

class CodeBase
{
    /**
     * @var \Magento_Shell
     */
    private $_shell;

    /**
     * @var \Zend_Log
     */
    private $_log;

    /**
     * @var string
     */
    private $_workingDir;

    /**
     * @var int
     */
    private $_lockLevel = 0;

    /**
     * Set dependencies and basic state parameters
     *
     * @param \Magento_Shell $shell
     * @param \Zend_Log $logger
     * @param string $workingDir
     * @throws \Exception
     */
    public function __construct(\Magento_Shell $shell, \Zend_Log $logger, $workingDir)
    {
        $this->_shell = $shell;
        $this->_log = $logger;
        if (!is_dir($workingDir)) {
            throw new \Exception("Working directory does not exist: '{$workingDir}'");
        }
        $this->_workingDir = $workingDir;
    }

    /**
     * Re-create the deployment directory and clone the Git repository from scratch
     */
    public function resetWorkingDir()
    {
        $gitCmd = 'git --work-tree=%s --git-dir=%s';
        $gitParams = array($this->_workingDir, $this->_workingDir . '/.git');
        $this->_shell->execute("{$gitCmd} reset --hard", $gitParams); // this will erase lock
        $this->_override();
        // restore the erased lock
        if ($this->_lockLevel > 0) {
            $this->_lock();
        }
    }

    /**
     * Delete the specified directory from deployment dir and create it
     *
     * @param string $dir relative to deployment dir
     * @return string absolute path to the affected directory
     */
    public function recreateDir($dir)
    {
        $this->removeDir($dir);
        $targetDir = "{$this->_workingDir}/{$dir}";
        $this->_log->log("mkdir('{$targetDir}')", \Zend_Log::INFO);
        mkdir($targetDir);
        return $targetDir;
    }

    /**
     * Remove a directory fromm deployment dir
     *
     * @param string $dir relative to the deployment dir
     * @throws \Exception
     */
    public function removeDir($dir)
    {
        if (empty($dir)) {
            throw new \Exception('Directory name must be not empty.');
        }
        $targetDir = "{$this->_workingDir}/{$dir}";
        $this->_rmDir($targetDir);
    }

    /**
     * Make code base non-accessible to the end-user
     *
     * @param bool $lock
     * @throws \Exception
     */
    public function setLock($lock = true)
    {
        if ($lock) {
            if ($this->_lockLevel == 0) {
                $this->_lock();
            }
            $this->_lockLevel++;
        } else {
            if ($this->_lockLevel == 1) {
                $this->_override(); // it will restore and patch .htaccess properly thus opening access back
            }
            if ($this->_lockLevel == 0) {
                throw new \Exception('Excessive unlock invoked');
            }
            $this->_lockLevel--;
        }
    }

    /**
     * Hack .htaccess to deny access to end-user
     */
    private function _lock()
    {
        $this->_patchFile('Allow from all', 'Deny from all', "{$this->_workingDir}/.htaccess");
    }

    /**
     * Impose build-specific files on code base
     *
     * Copy fresh index.build.php into the deployment dir and adjust the .htaccess accordingly
     */
    private function _override()
    {
        // custom entry point
        $source =  "{$this->_workingDir}/dev/build/saas_qa/index.build.php";
        $dest = "{$this->_workingDir}/index.build.php";
        $this->_log->log("copy({$source}, {$dest})", \Zend_Log::INFO);
        copy($source, $dest);

        // .htaccess
        $this->_shell->execute('git --work-tree=%s --git-dir=%s checkout -- .htaccess', array(
            $this->_workingDir, $this->_workingDir . '/.git'
        ));
        $this->_patchFile('index.php', 'index.build.php', "{$this->_workingDir}/.htaccess");
    }

    /**
     * Replace a string in file, similar to str_replace()
     *
     * @param string $search
     * @param string $replace
     * @param string $file
     */
    private function _patchFile($search, $replace, $file)
    {
        $this->_log->log("str_replace('{$search}', '{$replace}', {$file})", \Zend_Log::INFO);
        $contents = file_get_contents($file);
        $contents = str_replace($search, $replace, $contents);
        file_put_contents($file, $contents, LOCK_EX);
    }

    /**
     * Remove a directory using shell command
     *
     * @param string $dir
     */
    private function _rmDir($dir)
    {
        $isWindows = '\\' == DIRECTORY_SEPARATOR;
        if ($isWindows) {
            // workaround: on Windows sometimes there are random files still remaining
            for ($i = 0; $i < 2; $i++) {
                clearstatcache();
                if (is_dir($dir)) {
                    $this->_shell->execute('rmdir /S /Q %s', array($dir));
                    sleep(1);
                }
            }
        } else {
            $this->_shell->execute('rm -rf %s', array($dir));
        }
    }
}
