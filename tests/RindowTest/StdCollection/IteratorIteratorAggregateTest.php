<?php
namespace RindowTest\StdCollection\IteratorIteratorAggregateTest;

use PHPUnit\Framework\TestCase;
use Rindow\Stdlib\IteratorIteratorAggregate;

/**
 * @requires PHP 5.5.0
 */
class Test extends TestCase
{
    protected static $RINDOW_TEST_RESOURCES;
    public static function setUpBeforeClass()
    {
        self::$RINDOW_TEST_RESOURCES = __DIR__.'/../../resources';
        require self::$RINDOW_TEST_RESOURCES.'/php/php_5_5/IteratorIteratorAggregateTest.php';
    }

    public function testPlain()
    {
        $result = array();
        $generator = new IteratorIteratorAggregate(array('a'=>'A','b'=>'B','c'=>'C'));
        foreach ($generator as $key => $value) {
            $result[$key] = $value;
        }
        $this->assertEquals(array('a'=>'A','b'=>'B','c'=>'C'),$result);
    }

    public function testModify()
    {
        $result = array();
        $generator = new TestModifyer(array('a'=>'A','b'=>'B','c'=>'C'));
        foreach ($generator as $key => $value) {
            $result[$key] = $value;
        }
        $this->assertEquals(array(
                'Key(1000) a'=>'Hello A',
                'Key(1001) b'=>'Hello B',
                'Key(1002) c'=>'Hello C'),
            $result);
    }

    public function testPDOStatmentIterator()
    {
        $dsn = "sqlite:".__DIR__."/test.db.sqlite";
        $pdo = new \PDO($dsn);
        $pdo->exec("DROP TABLE IF EXISTS testdb");
        $pdo->exec("CREATE TABLE testdb ( id INTEGER PRIMARY KEY AUTOINCREMENT, name TEXT NOT NULL )");
        $pdo->exec("INSERT INTO testdb (name) values ('test1')");
        $pdo->exec("INSERT INTO testdb (name) values ('test2')");
        $stmt = $pdo->prepare("SELECT * FROM testdb");
        $stmt->setFetchMode(\PDO::FETCH_ASSOC);

        $iterator = new TestStatmentIterator($stmt);
        // calling "execute" after create IteratorIterator
        $stmt->execute();

        $count = 0;
        foreach ($iterator as $row) {
            $count += 1;
            if($count==1)
                $this->assertEquals(array('id'=>1,'name'=>'test1','modified'=>'Hello'),$row);
            if($count==2)
                $this->assertEquals(array('id'=>2,'name'=>'test2','modified'=>'Hello'),$row);
        }
        $this->assertEquals(2,$count);
    }

    public function testCallOutside()
    {
        $result = array();
        $generator = new TestModifyer(array('a'=>'A','b'=>'B','c'=>'C'));
        $iterator = $generator->getIterator();
        $this->assertEquals('Hello A',$iterator->current());
    }
}
