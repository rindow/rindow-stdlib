<?php
namespace RindowTest\FileUtil\FileLocatorTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\FileUtil\FileLocator;

class Test extends TestCase
{
    static $RINDOW_TEST_RESOURCES;
    public static function setUpBeforeClass()
    {
        self::$RINDOW_TEST_RESOURCES = __DIR__.'/../../resources';
    }

    public function testGetAllClassNames()
    {
    	$paths = array('Foo\\Bar'=>self::$RINDOW_TEST_RESOURCES.'/AcmeTest/FileUtil/filelocator/foobar');
    	$filelocator = new FileLocator($paths,'.orm.yml');
    	$classNames = $filelocator->getAllClassNames('GlobalBaseName');
    	$this->assertContains('Foo\Bar\ClassA',$classNames);
    	$this->assertContains('Foo\Bar\Sub\ClassB',$classNames);
    	$this->assertNotContains('Foo\Bar\Sub\GlobalBaseName',$classNames);
    }

    public function testFindMappingFileFound()
    {
    	$testbasepath = self::$RINDOW_TEST_RESOURCES.'/AcmeTest/FileUtil/filelocator';
    	$paths = array(
    		'Foo\\Bar'=>$testbasepath.'/foobar',
    		'Boo\\Baz'=>$testbasepath.'/boobaz',
    	);
    	$filelocator = new FileLocator($paths,'.orm.yml');
    	$this->assertEquals(
    		realpath($testbasepath.'/boobaz/ClassA.orm.yml'),
    		realpath($filelocator->findMappingFile('Boo\\Baz\\ClassA')));

    	$this->assertEquals(
    		realpath($testbasepath.'/boobaz/Sub/ClassB.orm.yml'),
    		realpath($filelocator->findMappingFile('Boo\\Baz\\Sub\\ClassB')));

    	$this->assertEquals(
    		realpath($testbasepath.'/foobar/ClassA.orm.yml'),
    		realpath($filelocator->findMappingFile('Foo\\Bar\\ClassA')));

    	$this->assertEquals(
    		realpath($testbasepath.'/foobar/Sub/ClassB.orm.yml'),
    		realpath($filelocator->findMappingFile('Foo\\Bar\\Sub\\ClassB')));
    }

    public function testFindMappingFileNotFound()
    {
        $testbasepath = self::$RINDOW_TEST_RESOURCES.'/AcmeTest/FileUtil/filelocator';
    	$paths = array(
    		'Foo\\Bar'=>$testbasepath.'/foobar',
    		'Boo\\Baz'=>$testbasepath.'/boobaz',
    	);
    	$filelocator = new FileLocator($paths,'.orm.yml');
    	$this->assertFalse($filelocator->findMappingFile('Boo\\Baz\\ClassB'));
    }

    public function testFileExists()
    {
        $testbasepath = self::$RINDOW_TEST_RESOURCES.'/AcmeTest/FileUtil/filelocator';
    	$paths = array(
    		'Foo\\Bar'=>$testbasepath.'/foobar',
    		'Boo\\Baz'=>$testbasepath.'/boobaz',
    	);
    	$filelocator = new FileLocator($paths,'.orm.yml');
    	$this->assertFalse($filelocator->fileExists('Boo\\Baz\\ClassB'));
    	$this->assertTrue($filelocator->fileExists('Boo\\Baz\\ClassA'));
    }
}
