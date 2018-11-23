<?php
namespace RindowTest\FileUtil\DirTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\FileUtil\Dir;

class Test extends TestCase
{
    static $RINDOW_TEST_RESOURCES;
    public static function setUpBeforeClass()
    {
        self::$RINDOW_TEST_RESOURCES = __DIR__.'/../../resources';
    }

    public function test()
    {
        $this->assertTrue(true);
        $this->markTestIncomplete();
        //var_dump(Dir::factory()->clawl(self::$RINDOW_TEST_RESOURCES));

        //var_dump(Dir::factory()->glob(self::$RINDOW_TEST_RESOURCES,'/.*php$/'));

        //Dir::factory()->glob(self::$RINDOW_TEST_RESOURCES,'/.*php$/',function($filename){
        //    var_dump($filename);
        //});

    }
}