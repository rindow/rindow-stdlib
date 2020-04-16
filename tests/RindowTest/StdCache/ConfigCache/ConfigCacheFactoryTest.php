<?php
namespace RindowTest\Stdlib\Cache\ConfigCache\ConfigCacheFactoryTest;

use PHPUnit\Framework\TestCase;
use ArrayObject;
use Rindow\Stdlib\Cache\SimpleCache\ArrayCache;
// Test Target Classes
use Rindow\Stdlib\Cache\ConfigCache\ConfigCacheFactory;

abstract class AbstractTestSimpleCache
{
	protected $config;
	public function __construct($config = null)
	{
		$this->config = $config;
	}
	public function getConfig($config)
	{
		return $this->config;
	}
	public function isReady()
	{
		return !isset($this->config['disable']);
	}
}

class TestSimpleCache1 extends AbstractTestSimpleCache
{}
class TestSimpleCache2 extends AbstractTestSimpleCache
{}


class Test extends TestCase
{
    public function is_apc_loaded()
    {
        if(!extension_loaded('apc') && !extension_loaded('apcu') && !ini_get('apc.enable_cli')) {
            return false;
        }
        return true;
    }

	public function testCreateDefault()
	{
        if(!$this->is_apc_loaded()) {
            $this->markTestSkipped('apc or apcu is not loaded');
            return;
        }
		$factory = new ConfigCacheFactory();
		$configCache = $factory->create('test\\path');
		$this->assertEquals('test/path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());

		$configCache = $factory->create('test\\path',$forceFileCache=true);
		$this->assertEquals('test/path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\FileCache',$configCache->getSecondary());

		$configCache = $factory->create('test\\path',$forceFileCache=true,$disableFileCache=true);
		$this->assertEquals('test/path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}

	public function testCreateDisableMemCache()
	{
		$config = array(
			'configCache'=>array(
				'enableMemCache' => false,
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\FileCache',$configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\FileCache',$configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true,$disableFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}

	public function testCreateDisableFileCache()
	{
        if(!$this->is_apc_loaded()) {
            $this->markTestSkipped('apc or apcu is not loaded');
            return;
        }
		$config = array(
			'configCache'=>array(
				'enableFileCache' => false,
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\FileCache',$configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true,$disableFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}

	public function testCreateDisableBothCache()
	{
		$config = array(
			'configCache'=>array(
				'enableMemCache' => false,
				'enableFileCache' => false,
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\FileCache',$configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true,$disableFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}

	public function testCreateForceFileCache()
	{
        if(!$this->is_apc_loaded()) {
            $this->markTestSkipped('apc or apcu is not loaded');
            return;
        }
		$config = array(
			'configCache'=>array(
				'forceFileCache' => true,
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\FileCache',$configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\FileCache',$configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true,$disableFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ApcCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}

	public function testCreateDisableCache()
	{
		$config = array(
			'configCache'=>array(
				'enableCache' => false,
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());

		$configCache = $factory->create('path',$forceFileCache=true,$disableFileCache=true);
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}

	public function testCreateDisableCache2()
	{
		$config = array(
			'enableCache' => false,
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertEquals('path',$configCache->getPath());
		$this->assertInstanceOf(
			'Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}

	public function testConfigureFileCache()
	{
		$config = array(
			'fileCache'=>array(
				'path' => 'filedir',
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path',$forceFileCache=true);
		$this->assertEquals('filedir',$configCache->getSecondary()->getPath());
	}

	public function testConfigureFileCache2()
	{
		$config = array(
			'filePath' => 'filedir',
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path',$forceFileCache=true);
		$this->assertEquals('filedir',$configCache->getSecondary()->getPath());
	}

	public function testConfigureMemCache()
	{
        if(!$this->is_apc_loaded()) {
            $this->markTestSkipped('apc or apcu is not loaded');
            return;
        }
		$config = array(
			'memCache'=>array(
				'foo' => 'bar',
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertEquals(array('foo'=>'bar'),$configCache->getPrimary()->getConfig());
	}

	public function testAlternateCache()
	{
		$config = array(
			'memCache'=>array(
				'class' => __NAMESPACE__.'\TestSimpleCache1',
			),
			'fileCache'=>array(
				'class' => __NAMESPACE__.'\TestSimpleCache2',
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path',$forceFileCache=true);
		$this->assertInstanceOf(__NAMESPACE__.'\TestSimpleCache1',$configCache->getPrimary());
		$this->assertInstanceOf(__NAMESPACE__.'\TestSimpleCache2',$configCache->getSecondary());
	}

	public function testMemCacheExtensionNotLoaded()
	{
		$config = array(
			'memCache'=>array(
				'class' => __NAMESPACE__.'\TestSimpleCache1',
				'disable' => true,
			),
			'fileCache'=>array(
				'class' => __NAMESPACE__.'\TestSimpleCache2',
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertInstanceOf('Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertInstanceOf(__NAMESPACE__.'\TestSimpleCache2',$configCache->getSecondary());
	}

	public function testFileCacheExtensionNotLoaded()
	{
		$config = array(
			'memCache'=>array(
				'class' => __NAMESPACE__.'\TestSimpleCache1',
			),
			'fileCache'=>array(
				'class' => __NAMESPACE__.'\TestSimpleCache2',
				'disable' => true,
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertInstanceOf(__NAMESPACE__.'\TestSimpleCache1',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}

	public function testBothCacheExtensionNotLoaded()
	{
		$config = array(
			'memCache'=>array(
				'class' => __NAMESPACE__.'\TestSimpleCache1',
				'disable' => true,
			),
			'fileCache'=>array(
				'class' => __NAMESPACE__.'\TestSimpleCache2',
				'disable' => true,
			),
		);
		$factory = new ConfigCacheFactory($config);
		$configCache = $factory->create('path');
		$this->assertInstanceOf('Rindow\Stdlib\Cache\SimpleCache\ArrayCache',$configCache->getPrimary());
		$this->assertNull($configCache->getSecondary());
	}
}
