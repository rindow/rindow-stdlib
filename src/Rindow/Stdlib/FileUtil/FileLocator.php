<?php
namespace Rindow\Stdlib\FileUtil;

use Rindow\Stdlib\FileUtil\Dir;
use Rindow\Stdlib\FileUtil\Exception;

class FileLocator
{
    protected $paths;
    protected $fileExtension = '.php';

    public function __construct(array $paths,$fileExtension=null)
    {
        $this->paths = $paths;
        if($fileExtension)
            $this->fileExtension = $fileExtension;
    }

    /**
     * Locates mapping file for the given class name.
     *
     * @param string $className
     *
     * @return string
     */
    public function findMappingFile($className)
    {
        foreach ($this->paths as $namespace => $path) {
            if(strpos($className, $namespace.'\\')!==0)
                continue;
            $fileName = substr($className, strlen($namespace));
            $fileName = str_replace('\\', DIRECTORY_SEPARATOR, $fileName) . $this->fileExtension;
            $path = (array)$path;
            foreach ($path as $p) {
                if (is_file($p . $fileName)) {
                    return $p . $fileName;
                }
            }
        }
        return false;
    }

    /**
     * Gets all class names that are found with this file locator.
     *
     * @param string $globalBasename Passed to allow excluding the basename.
     *
     * @return array
     */
    public function getAllClassNames($globalBasename)
    {
        $dir = new Dir();
        $pattern = '@'.str_replace('.', '\\.', $this->fileExtension).'$@';
        $classNames = array();
        $fileExtension = $this->fileExtension;
        foreach($this->paths as $namespace => $path) {
            $path = (array)$path;
            foreach ($path as $p) {
                $realpath = realpath($p);
                $classNames = array_merge(
                    $classNames,
                    $dir->glob($p,$pattern,function($fullfilename) use ($realpath,$namespace,$fileExtension,$globalBasename) {
                        if(basename($fullfilename,$fileExtension) == $globalBasename)
                            return null;
                        $fullfilename = realpath($fullfilename);
                        $className = substr($fullfilename,strlen($realpath)+1);
                        $className = substr($className,0,strlen($className)-strlen($fileExtension));
                        $className = $namespace.'\\'.str_replace('/', '\\', $className);
                        return $className;
                }));
            }
        }
        return $classNames;
    }

    /**
     * Checks if a file can be found for this class name.
     *
     * @param string $className
     *
     * @return bool
     */
    public function fileExists($className)
    {
        if(false===$this->findMappingFile($className))
            return false;
        return true;
    }

    /**
     * Gets all the paths that this file locator looks for mapping files.
     *
     * @return array
     */
    public function getPaths()
    {
        return $this->paths;
    }

    /**
     * Gets the file extension that mapping files are suffixed with.
     *
     * @return string
     */
    public function getFileExtension()
    {
        return $this->fileExtension;
    }
}
