<?php
namespace RindowTest\Entity\HydratorTest;

use PHPUnit\Framework\TestCase;
use stdClass;

// Test Target Classes
use Rindow\Stdlib\Entity\AbstractEntity;
use Rindow\Stdlib\Entity\EntityHydrator;
use Rindow\Stdlib\Entity\ReflectionHydrator;
use Rindow\Stdlib\Entity\PropertyHydrator;

use AcmeTest\Entity\Bean2;

class Product
{
    protected $id;
    protected $name;
    private   $privateVar;

    public function getId()
    {
        return $this->id;
    }
    public function getName()
    {
        return $this->name;
    }
    public function getPrivateVar()
    {
        return $this->privateVar;
    }
}

class Bean1 extends AbstractEntity
{
    protected $__propertyAccess = array('allow'=>true);

    protected $id;
    protected $name;
    private   $privateVar;
    protected $allow;

    public function getId()
    {
        return $this->id;
    }
    // a getter is not defined for name
}


class Object1 
{
    protected $id;
    public    $name;
    private   $privateVar;

    public function getId()
    {
        return $this->id;
    }
    // a getter is not defined for name
}

class Rindow2HydratorTest extends TestCase
{
    static $RINDOW_TEST_RESOURCES;
    public static function setUpBeforeClass()
    {
        self::$RINDOW_TEST_RESOURCES = __DIR__.'/../../resources';
    }

    public function setUp()
    {
    }
    public function testHydratorReflection()
    {

        $std = new stdClass();
        $std->id = 1;
        $std->name = 'abc';
        $std->privateVar = 'def';
        $array = get_object_vars($std);

        $product = new Product();

        $hydrator = new ReflectionHydrator();

        $hydrator->hydrate($array,$product);
        $this->assertEquals($array['id'], $product->getId());
        $this->assertEquals($array['name'], $product->getName());
        $this->assertEquals($array['privateVar'], $product->getPrivateVar());

        $std2 = $hydrator->extract($product);
        $this->assertEquals($std2['id'], $product->getId());
        $this->assertEquals($std2['name'], $product->getName());
        $this->assertEquals($std2['privateVar'], $product->getPrivateVar());
    }

    public function testHydratorBean()
    {
        $std = new stdClass();
        $std->id = 1;
        $std->name = 'abc';
        $std->privateVar = 'def';
        $array = get_object_vars($std);

        $bean1 = new Bean1();

        $bean1->hydrate($array);
        $this->assertEquals($array['id'], $bean1->getId());
        $this->assertEquals($array['name'], $bean1->getName());
        //$this->assertEquals($array['privateVar'], $bean1->getPrivateVar());

        $std2 = $bean1->extract();
        $this->assertEquals($std2['id'], $bean1->getId());
        $this->assertEquals($std2['name'], $bean1->getName());

        $bean1->setName('xyz');
        $this->assertEquals('xyz', $bean1->getName());

        $std3 = $bean1->extract();
        $this->assertEquals('xyz',$std3['name']);
    }

    /**
     * @expectedException        Rindow\Stdlib\Entity\Exception\DomainException
     * @expectedExceptionMessage Property "abc" is not found in RindowTest
     */
    public function testHydratorBeanNotFoundAccessViolationToRead()
    {
        $bean1 = new Bean1();
        $bean1->getAbc();
    }

    /**
     * @expectedException        Rindow\Stdlib\Entity\Exception\DomainException
     * @expectedExceptionMessage Property "abc" is not found in RindowTest
     */
    public function testHydratorBeanNotFoundAccessViolationToWrite()
    {
        $bean1 = new Bean1();
        $bean1->setAbc();
    }

    /**
     * @expectedException        Rindow\Stdlib\Entity\Exception\DomainException
     * @expectedExceptionMessage a property is read only:id
     */
    public function testHydratorBeanPrivateAccessViolation()
    {
        $bean1 = new Bean1();
        $bean1->setId(100);
    }

    /**
     * @expectedException PHPUnit_Framework_Error
     */
    public function PassForErrortestHydratorBeanReadOnlyAccessViolation()
    {
        // PHPUnit can not avoid the error
        $bean1 = new Bean1();
        $bean1->getPrivateVar();
    }

    /**
     * @requires PHP 5.4.0
     */
    public function testHydratorBeanTrait()
    {
        require_once self::$RINDOW_TEST_RESOURCES.'/AcmeTest/Entity/entity_with_trait.php';
        $std = new stdClass();
        $std->id = 1;
        $std->name = 'abc';
        $std->privateVar = 'def';
        $array = get_object_vars($std);

        $bean1 = new Bean2();

        $bean1->hydrate($array);
        $this->assertEquals($array['id'], $bean1->getId());
        $this->assertEquals($array['name'], $bean1->getName());
        $this->assertEquals($array['privateVar'], $bean1->getPrivateVar());

        $std2 = $bean1->extract();
        $this->assertEquals($std2['id'], $bean1->getId());
        $this->assertEquals($std2['name'], $bean1->getName());
        $this->assertEquals($std2['privateVar'], $bean1->getPrivateVar());

        $bean1->setName('xyz');
        $this->assertEquals('xyz', $bean1->getName());
        $bean1->setPrivateVar('opq');
        $this->assertEquals('opq', $bean1->getPrivateVar());

        $std3 = $bean1->extract();
        $this->assertEquals('xyz',$std3['name']);
        $this->assertEquals('opq',$std3['privateVar']);
    }

    /**
     * @requires PHP 5.4.0
     * @expectedException        Rindow\Stdlib\Entity\Exception\DomainException
     * @expectedExceptionMessage Property "abc" is not found in AcmeTest
     */
    public function testHydratorBeanTraitNotFoundAccessViolationToRead()
    {
        require_once self::$RINDOW_TEST_RESOURCES.'/AcmeTest/Entity/entity_with_trait.php';
        $bean1 = new Bean2();
        $bean1->getAbc();
    }

    /**
     * @requires PHP 5.4.0
     * @expectedException        Rindow\Stdlib\Entity\Exception\DomainException
     * @expectedExceptionMessage Property "abc" is not found in AcmeTest
     */
    public function testHydratorBeanTraitNotFoundAccessViolationToWrite()
    {
        require_once self::$RINDOW_TEST_RESOURCES.'/AcmeTest/Entity/entity_with_trait.php';
        $bean1 = new Bean2();
        $bean1->setAbc();
    }

    /**
     * @requires PHP 5.4.0
     * @expectedException        Rindow\Stdlib\Entity\Exception\DomainException
     * @expectedExceptionMessage a property is read only:id
     */
    public function testHydratorBeanTraitReadOnlyAccessViolation()
    {
        require_once self::$RINDOW_TEST_RESOURCES.'/AcmeTest/Entity/entity_with_trait.php';
        $bean1 = new Bean2();
        $bean1->setId(100);
    }

    public function testHydratorEntityHydrator()
    {
        // Same as ReflectionHydrator

        $std = new stdClass();
        $std->id = 1;
        $std->name = 'abc';
        $array = get_object_vars($std);

        $product = new Bean1();

        $hydrator = new EntityHydrator();

        $hydrator->hydrate($array,$product);
        $this->assertEquals($array['id'], $product->getId());
        $this->assertEquals($array['name'], $product->getName());

        $std2 = $hydrator->extract($product);
        $this->assertEquals($std2['id'], $product->getId());
        $this->assertEquals($std2['name'], $product->getName());
    }

    public function testHydratorPropertyHydrator()
    {
        $std = new stdClass();
        $std->id = 1;
        $std->name = 'abc';
        $array = get_object_vars($std);

        $product = new Object1();

        $hydrator = new PropertyHydrator();

        $hydrator->hydrate($array,$product);
        $this->assertNull($product->getId());
        $this->assertEquals($array['name'], $product->name);

        $std2 = $hydrator->extract($product);
        $this->assertFalse(array_key_exists('id', $std2));
        $this->assertEquals($std2['name'], $product->name);
    }

    public function testProtectedProperty()
    {
        $bean = new Bean1();
        $bean->allow = 1;
        $this->assertEquals(1,$bean->allow);
    }
}
