<?php
namespace Rindow\Stdlib\Cache;

use ArrayAccess;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

class FileCache implements ArrayAccess
{
    protected $cachePath;

    public static function getClassName()
    {
        return __CLASS__;
    }

    public static function isReady()
    {
        return true;
    }

    public static function clear($path=null)
    {
        if(!file_exists($path)) {
            return;
        }
        $fileSPLObjects = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path),
                RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach($fileSPLObjects as $fullFileName => $fileSPLObject) {
            $filename = $fileSPLObject->getFilename();
            if (is_dir($fullFileName)) {
                if($filename!='.' && $filename!='..') {
                    @rmdir($fullFileName);
                }
            } else {
                @unlink($fullFileName);
            }
        }
    }

    public static function getDefaultFileCachePath()
    {
        return sys_get_temp_dir();
    }

    public function __construct($cachePath=null)
    {
        $this->setCachePath($cachePath);
    }
    
    public function setCachePath($cachePath)
    {
        $this->cachePath = $cachePath;
        return $this;
    }
    
    public function getCachePath()
    {
        return $this->cachePath;
    }

    public function transFromOffsetToPath($offset)
    {
        return str_replace(
            array('\\',':',  '*',  '?',  '"',  '<',  '>',  '|',  '.'),
            array('/', '%3A','%2A','%3F','%22','%3C','%3E','%7C','%46'),
            $offset);
    }

    public function setTimeOut($timeOut)
    {
        return $this;
    }

    public function offsetExists($offset)
    {
        $filename = $this->cachePath . '/' . $this->transFromOffsetToPath($offset) . '.php';
        return file_exists($filename);
    }

    public function offsetGet($offset)
    {
        $filename = $this->cachePath . '/' . $this->transFromOffsetToPath($offset) . '.php';
        if(!file_exists($filename))
            return false;
        return require $filename;
    }

    public function offsetSet($offset,$value)
    {
        $code = "<?php\nreturn unserialize('".str_replace(array('\\','\''), array('\\\\','\\\''), serialize($value))."');";
        //$code = "<?php\nreturn unserialize(\"".str_replace(array("\\","\0","\"","\n","\r","\t"), array("\\\\","\\0","\\\"","\\n","\\r","\\t"), serialize($value))."\");";
        //$code = "<?php\nreturn unserialize(base64_decode('".base64_encode(serialize($value))."'));";
        $filename = $this->cachePath . '/' . $this->transFromOffsetToPath($offset) . '.php';
        if(!is_dir(dirname($filename))) {
            $dirname = dirname($filename);
            mkdir(dirname($filename),0777,true);
        }
        file_put_contents($filename, $code);
        return $this;
    }

    public function offsetUnset($offset)
    {
        $filename = $this->cachePath . '/' . $this->transFromOffsetToPath($offset) . '.php';
        if(!file_exists($filename))
            return false;
        unlink($filename);
        return $this;
    }

    public function containsKey($offset)
    {
        return $this->offsetExists($offset);
    }

    public function get($offset,$default=null,$callback=null)
    {
        $filename = $this->cachePath . '/' . $this->transFromOffsetToPath($offset) . '.php';
        if(file_exists($filename))
            return require $filename;
        if($callback==null)
            return $default;
        $value = $default;
        $args = array($this, $offset, &$value);
        if(call_user_func_array($callback,$args)) {
            $this->put($offset,$value);
        }
        return $value;
    }

    public function put($offset, $value, $addMode=null)
    {
        if($addMode) {
            if($this->offsetExists($offset))
                return $this;
        }
        $this->offsetSet($offset,$value);
        return $this;
    }

    public function remove($offset)
    {
        $this->offsetUnset($offset);
        return $this;
    }

    public function hasFileStorage()
    {
        return true;
    }
}