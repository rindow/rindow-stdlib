<?php
namespace Rindow\Stdlib\FileUtil;

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class Dir
{
    public static function factory()
    {
        return new self();
    }

    public function clawl($path,$callback=null)
    {
        return $this->glob($path,null,$callback);
    }

    public function glob($path,$pattern,$callback=null)
    {
        if(!file_exists($path)) {
            throw new Exception\DomainException('directory not found: '.$path);
        }
        $fileSPLObjects = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path)
        );
        $filenames = array();
        foreach($fileSPLObjects as $fullFileName => $fileSPLObject) {
            $filename = $fileSPLObject->getFilename();
            if (!is_dir($fullFileName)) {
                if($filename!='.' && $filename!='..') {
                    if($pattern==null || preg_match($pattern, $fullFileName)) {
                        if($callback) {
                            $f = call_user_func($callback,$fullFileName);
                            if($f!==null)
                                $filenames[] = $f;
                        }
                        else {
                            $filenames[] = $fullFileName;
                        }
                    }
                }
            }
        }
        return $filenames;
    }
}
