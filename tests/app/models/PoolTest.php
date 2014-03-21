<?php
/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-19 at 22:27:28.
 */
class PoolTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Pool
     */
    protected $pool;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
		$this->pool = new Pool("TestPool", "localhost", "username", "password", "scryp");
		Database::initDb();
		$this->pool->persist();

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {

    }

    /**
     * @covers Pool::findByName
     * @todo   Implement testFindByName().
     */
    public function testFindByName()
	{
		$result = Pool::findByName($this->pool->name);
		$this->assertEquals($result->id,$this->pool->id);
    }

    /**
     * @covers Pool::findById
     * @todo   Implement testFindById().
     */
    public function testFindById()
    {
		$result = Pool::findById($this->pool->id);
		$this->assertEquals($result->name, $this->pool->name);
    }
	public function testFindAll() {
		$pool = new Pool("TestPool3", "localhost", "username", "password", "scryp");
		$pool->persist();
		$result = Pool::findAll();
		$this->assertGreaterThan(0, sizeof($result));
		$pool->delete();

	}

    /**
     * @covers Pool::persist
     * @todo   Implement testPersist().
     */
    public function testPersist()
    {
		$new_pool = new Pool("NewPool", "localhost", "username", "password", "scryp");
		$new_pool->persist();
		$res = Pool::findByName($new_pool->name);
		$this->assertEquals($new_pool->id, $res->id);
		$new_pool->delete();

    }

    /**
     * @covers Pool::delete
     * @todo   Implement testDelete().
     */
    public function testDelete()
	{
		$this->pool->delete();
		$result = Pool::findById($this->pool->id);
		$this->assertEquals($result,NULL);

    }
}