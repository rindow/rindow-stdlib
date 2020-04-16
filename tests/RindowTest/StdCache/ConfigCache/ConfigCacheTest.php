<?php
namespace RindowTest\Stdlib\Cache\ConfigCache\ConfigCacheTest;

use PHPUnit\Framework\TestCase;
use ArrayObject;
use Rindow\Stdlib\Cache\SimpleCache\ArrayCache;
// Test Target Classes
use Rindow\Stdlib\Cache\ConfigCache\ConfigCache;

class Test extends TestCase
{
	public function testNormalSetGet()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertEquals('A',$secondary->get('path/a'));
		$this->assertEquals('A',$cache->get('a'));
	}

	public function testHitPrimaryCacheSetGet()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$secondary->delete('path/a');
		$this->assertEquals('A',$cache->get('a'));
	}

	public function testExpirePrimaryCacheSetGet()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$primary->delete('path/a');
		$this->assertNull($primary->get('path/a'));
		$this->assertEquals('A',$cache->get('a'));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
	}

	public function testHitNoItemPrimaryCacheSetGet()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertEquals('default',$cache->get('a','default'));
		$this->assertEquals(array(false,null),$primary->get('path/a'));
		$this->assertFalse($secondary->has('path/a'));

		$secondary->set('path/a','NONEDATA');
		$this->assertEquals('default',$cache->get('a','default'));
	}

	public function testExpireNoItemPrimaryCacheSetGet()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertEquals('default',$cache->get('a','default'));
		$this->assertEquals(array(false,null),$primary->get('path/a'));
		$this->assertFalse($secondary->has('path/a'));

		$primary->delete('path/a');
		$this->assertEquals('default',$cache->get('a','default'));
		$this->assertEquals(array(false,null),$primary->get('path/a'));
		$this->assertFalse($secondary->has('path/a'));
	}

	public function testNormalSetHasDelete()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertEquals('A',$secondary->get('path/a'));
		$this->assertTrue($cache->has('a'));

		$this->assertTrue($cache->delete('a'));
		$this->assertFalse($primary->has('path/a'));
		$this->assertFalse($secondary->has('path/a'));
		$this->assertFalse($cache->has('a'));
		$this->assertFalse($cache->delete('a'));
	}

	public function testHitPrimaryCacheSetHasDelete()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$secondary->delete('path/a');
		$this->assertTrue($cache->has('a'));
	}

	public function testExpirePrimaryCacheSetHas()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$primary->delete('path/a');
		$this->assertFalse($primary->has('path/a'));
		$this->assertTrue($cache->has('a'));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
	}

	public function testHitNoItemPrimaryCacheSetHas()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertFalse($cache->has('a'));
		$this->assertEquals(array(false,null),$primary->get('path/a'));
		$this->assertFalse($secondary->has('path/a'));

		$secondary->set('path/a','NONEDATA');
		$this->assertFalse($cache->has('a'));
	}

	public function testExpireNoItemPrimaryCacheSetHas()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertFalse($cache->has('a'));
		$this->assertEquals(array(false,null),$primary->get('path/a'));
		$this->assertFalse($secondary->has('path/a'));

		$primary->delete('path/a');
		$this->assertFalse($cache->has('a'));
		$this->assertEquals(array(false,null),$primary->get('path/a'));
		$this->assertFalse($secondary->has('path/a'));
	}

	public function testNormalSetGetWithoutSecondary()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$cache = new ConfigCache($path,$primary);

		$this->assertTrue($cache->set('a','A'));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertEquals('A',$cache->get('a'));
	}

	public function testNoItemPrimaryCacheSetGetWithoutSecondary()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$cache = new ConfigCache($path,$primary);

		$this->assertEquals('default',$cache->get('a','default'));
		// ** CAUTION: primary cache do not save the state
		//             when it has no secondary cache.
		$this->assertNull($primary->get('path/a'));

		$this->assertEquals('default',$cache->get('a','default'));
	}

	public function testNormalSetHasDeleteWithoutSecondary()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$cache = new ConfigCache($path,$primary);

		$this->assertTrue($cache->set('a','A'));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertTrue($cache->has('a'));

		$this->assertTrue($cache->delete('a'));
		$this->assertFalse($primary->has('path/a'));
		$this->assertFalse($cache->has('a'));
	}

	public function testNoItemPrimaryCacheSetHasWithoutSecondary()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$cache = new ConfigCache($path,$primary);

		$this->assertFalse($cache->has('a'));
		// ** CAUTION: primary cache do not save the state
		//             when it has no secondary cache.
		$this->assertNull($primary->get('path/a'));

		$this->assertFalse($cache->has('a'));
	}

	public function testNormalGetEx()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertEquals('A',$cache->getEx('a',function($key,$arguments,&$save){
			return $arguments['default'];
		},$arguments=array('default'=>'A')));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertEquals('A',$secondary->get('path/a'));
		$this->assertEquals('A',$cache->get('a'));
	}

	public function testHitPrimaryCacheGetEx()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$secondary->delete('path/a');
		$this->assertEquals('A',$cache->getEx('a',function($key,$arguments,&$save){
			return $arguments['default'];
		},$arguments=array('default'=>'DUMMY')));
	}

	public function testExpirePrimaryCacheGetEx()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$primary->delete('path/a');
		$this->assertNull($primary->get('path/a'));
		$this->assertEquals('A',$cache->getEx('a',function($key,$arguments,&$save){
			return $arguments['default'];
		},$arguments=array('default'=>'DUMMY')));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
	}

	public function testHitNoItemPrimaryCacheGetEx()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertEquals('DUMMY',$cache->get('a','DUMMY'));
		$this->assertEquals(array(false,null),$primary->get('path/a'));
		$this->assertFalse($secondary->has('path/a'));

		$this->assertEquals('A',$cache->getEx('a',function($key,$arguments,&$save){
			return $arguments['default'];
		},$arguments=array('default'=>'A')));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertEquals('A',$secondary->get('path/a'));
	}

	public function testExpireNoItemPrimaryCacheGetEx()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertEquals('DUMMY',$cache->get('a','DUMMY'));
		$this->assertEquals(array(false,null),$primary->get('path/a'));
		$this->assertFalse($secondary->has('path/a'));

		$primary->delete('path/a');
		$this->assertEquals('A',$cache->getEx('a',function($key,$arguments,&$save){
			return $arguments['default'];
		},$arguments=array('default'=>'A')));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertEquals('A',$secondary->get('path/a'));
	}

	public function testNormalGetExWithoutSecondary()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$cache = new ConfigCache($path,$primary);

		$this->assertEquals('A',$cache->getEx('a',function($key,$arguments,&$save){
			return $arguments['default'];
		},$arguments=array('default'=>'A')));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertEquals('A',$cache->get('a'));
	}

	public function testHitPrimaryCacheGetExWithoutSecondary()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$cache = new ConfigCache($path,$primary);

		$this->assertTrue($cache->set('a','A'));
		$this->assertEquals('A',$cache->getEx('a',function($key,$arguments,&$save){
			return $arguments['default'];
		},$arguments=array('default'=>'DUMMY')));
	}

	public function testHitNoItemPrimaryCacheGetExWithoutSecondary()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$cache = new ConfigCache($path,$primary);

		$this->assertEquals('DUMMY',$cache->get('a','DUMMY'));
		// ** CAUTION: primary cache do not save the state
		//             when it has no secondary cache.
		$this->assertNull($primary->get('path/a'));

		$this->assertEquals('A',$cache->getEx('a',function($key,$arguments,&$save){
			return $arguments['default'];
		},$arguments=array('default'=>'A')));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
	}

    public function testMultipleNormal()
    {

		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

        $values = array('a'=>'A','b'=>'B','c'=>'C');
        $this->assertTrue($cache->setMultiple($values));

        $keys = array('a','b','c','d');
        $this->assertEquals(array('a'=>'A','b'=>'B','c'=>'C','d'=>'default'),
            $cache->getMultiple($keys,'default'));

        $keys = array('b','c');
        $this->assertTrue($cache->deleteMultiple($keys));

        $keys = array('a','b','c','d');
        $this->assertEquals(array('a'=>'A','b'=>'default','c'=>'default','d'=>'default'),
            $cache->getMultiple($keys,'default'));

        $this->assertTrue($cache->has('a'));
        $keys = array('b','c','a');
        $this->assertFalse($cache->deleteMultiple($keys));
        $this->assertFalse($cache->has('a'));

        // *** CAUTION ***
        // ttl test can not be done with phpunit
        //
    }

    public function testMultipleTraversableNormal()
    {
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

        $values = new ArrayObject(array('a'=>'A','b'=>'B','c'=>'C'));
        $this->assertTrue($cache->setMultiple($values));

        $keys = new ArrayObject(array('a','b','c','d'));
        $this->assertEquals(array('a'=>'A','b'=>'B','c'=>'C','d'=>'default'),
            $cache->getMultiple($keys,'default'));

        $keys = new ArrayObject(array('b','c'));
        $this->assertTrue($cache->deleteMultiple($keys));
    }

	public function testIsReady()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);
		$this->assertTrue($cache->isReady());
    }

	public function testClear()
	{
		$path = 'path';
		$primary = new ArrayCache();
		$secondary = new ArrayCache();
		$cache = new ConfigCache($path,$primary,$secondary);

		$this->assertTrue($cache->set('a','A'));
		$this->assertEquals(array(true,'A'),$primary->get('path/a'));
		$this->assertEquals('A',$secondary->get('path/a'));
		$this->assertTrue($cache->clear('a'));
		$this->assertFalse($primary->has('path/a'));
		$this->assertFalse($secondary->has('path/a'));
	}
}
