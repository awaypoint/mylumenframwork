<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateModules extends Command
{
    const MODULE_BASE_PATH = APP_PATH . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR . 'Modules' . DIRECTORY_SEPARATOR;

    private $_moduleName;
    /**
     * 控制台命令名称
     *
     * @var string
     */
    protected $signature = 'make:modules {name}';

    /**
     * 控制台命令描述
     *
     * @var string
     */
    protected $description = 'Create a new module';

    /**
     * 执行控制台命令
     *
     * @return mixed
     */
    public function handle()
    {
        $this->_moduleName = ucfirst($this->argument('name'));
        $modulesPath = self::MODULE_BASE_PATH . $this->_moduleName;
        if (file_exists($modulesPath)) {
            echo 'module is exists';
            return;
        }
        $this->copyDir(self::MODULE_BASE_PATH . 'Example', $modulesPath);
        $this->modifyFile($modulesPath);
        echo 'success';
    }

    /**
     * 复制文件
     * @param $src
     * @param $destination
     */
    public function copyDir($src, $destination)
    {
        $dir = opendir($src);
        mkdir($destination, 777, true);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . DIRECTORY_SEPARATOR . $file)) {
                    $this->copyDir($src . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file);
                    continue;
                } else {
                    $fileName = str_replace('Example', $this->_moduleName, $file);
                    copy($src . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $fileName);
                }
            }
        }
        closedir($dir);
    }

    /**
     * 修改文件
     * @param $src
     */
    public function modifyFile($src)
    {
        $dir = opendir($src);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                $fileName = $src . DIRECTORY_SEPARATOR . $file;
                if (is_dir($fileName)) {
                    $this->modifyFile($fileName);
                } else {
                    $lowModuleName = strtolower($this->_moduleName);
                    $content = file_get_contents($fileName);
                    $content = str_replace('_bigname_', $this->_moduleName, $content);
                    $content = str_replace('_smallname_', $lowModuleName, $content);
                    file_put_contents($fileName, $content);
                }
            }
        }
        closedir($dir);
    }
}