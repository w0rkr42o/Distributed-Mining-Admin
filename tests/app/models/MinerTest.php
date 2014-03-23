<?php

/**
 * Generated by PHPUnit_SkeletonGenerator 1.2.1 on 2014-03-01 at 14:37:34.
 */
class MinerTest extends PHPUnit_Framework_TestCase {

    /**
     * @var Miner
     */
    protected $miner;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp() {

        $this->miner = new Miner('localhost', 4028);
		Database::initDb();
    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown() {
        $this->miner = null;
    }


    /**
     * @covers Miner::request
     * @todo   Implement testRequest().
     */
    public function testRequest() {

        $res = $this->miner->request('{"command":"version","parameter":""}');
        $this->assertEquals('4.0.0', $res['VERSION'][0]['SGMiner']);
        
    }
    
    public function testCreateReading() {
        $reading = $this->miner->getReading();
        $this->assertGreaterThan(0, $reading->temp[0]);
        $this->assertGreaterThan(0, $reading->hashSpeed);
    }
	public function testListPools(){
		$pools = $this->miner->listPools();
		$this->assertGreaterThan(0, sizeof($pools));

	}
	public function testSwitchPool() {
	$this->miner->switchPool(1);
	$this->miner->switchPool(0);
	}

	public function testSwitchPoolByName() {
		$pool = Pool::findByName("Trademybit EU");
		$this->miner->switchPoolByName($pool->name);
	}

}
