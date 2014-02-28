<?php
/**
 * DBConfigTask Test Case
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 1.3
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Console\Command\Task;

use Cake\Console\Command\Task\DbConfigTask;
use Cake\Core\Configure;
use Cake\TestSuite\TestCase;

/**
 * DbConfigTest class
 *
 */
class DbConfigTaskTest extends TestCase {

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$out = $this->getMock('Cake\Console\ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('Cake\Console\ConsoleInput', array(), array(), '', false);

		$this->Task = $this->getMock('Cake\Console\Command\Task\DbConfigTask',
			array('in', 'out', 'err', 'hr', 'createFile', '_stop', '_checkUnitTest', '_verify'),
			array($out, $out, $in)
		);

		$this->Task->path = APP . 'Config/';
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Task);
	}

/**
 * Test the getConfig method.
 *
 * @return void
 */
	public function testGetConfig() {
		$this->Task->expects($this->any())
			->method('in')
			->will($this->returnValue('test'));

		$result = $this->Task->getConfig();
		$this->assertEquals('test', $result);
	}

/**
 * test that initialize sets the path up.
 *
 * @return void
 */
	public function testInitialize() {
		$this->Task->initialize();
		$this->assertFalse(empty($this->Task->path));
		$this->assertEquals(APP . 'Config/', $this->Task->path);
	}

/**
 * test execute and by extension _interactive
 *
 * @return void
 */
	public function testExecuteIntoInteractive() {
		$this->Task->initialize();

		$out = $this->getMock('Cake\Console\ConsoleOutput', array(), array(), '', false);
		$in = $this->getMock('Cake\Console\ConsoleInput', array(), array(), '', false);
		$this->Task = $this->getMock(
			'Cake\Console\Command\Task\DbConfigTask',
			array('in', '_stop', 'createFile'), array($out, $out, $in)
		);
		$this->Task->path = APP . 'Config' . DS;

		$expected = "<?php\n";
		$expected .= "namespace App\Config;\n";
		$expected .= "use Cake\Core\Configure;\n\n";
		$expected .= "Configure::write('Datasource.default', [\n";
		$expected .= "\t'datasource' => 'Database/mysql',\n";
		$expected .= "\t'persistent' => false,\n";
		$expected .= "\t'host' => 'localhost',\n";
		$expected .= "\t'login' => 'root',\n";
		$expected .= "\t'password' => 'password',\n";
		$expected .= "\t'database' => 'cake_test',\n";
		$expected .= "]);\n";

		$this->Task->expects($this->once())->method('_stop');
		$this->Task->expects($this->at(0))->method('in')->will($this->returnValue('default')); //name
		$this->Task->expects($this->at(1))->method('in')->will($this->returnValue('mysql')); //db type
		$this->Task->expects($this->at(2))->method('in')->will($this->returnValue('n')); //persistent
		$this->Task->expects($this->at(3))->method('in')->will($this->returnValue('localhost')); //server
		$this->Task->expects($this->at(4))->method('in')->will($this->returnValue('n')); //port
		$this->Task->expects($this->at(5))->method('in')->will($this->returnValue('root')); //user
		$this->Task->expects($this->at(6))->method('in')->will($this->returnValue('password')); //password
		$this->Task->expects($this->at(10))->method('in')->will($this->returnValue('cake_test')); //db
		$this->Task->expects($this->at(11))->method('in')->will($this->returnValue('n')); //prefix
		$this->Task->expects($this->at(12))->method('in')->will($this->returnValue('n')); //encoding
		$this->Task->expects($this->at(13))->method('in')->will($this->returnValue('y')); //looks good
		$this->Task->expects($this->at(14))->method('in')->will($this->returnValue('n')); //another
		$this->Task->expects($this->at(15))->method('createFile')
			->with(
				$this->equalTo($this->Task->path . 'datasources.php'),
				$this->equalTo($expected));

		Configure::write('Datasource', array());
		$this->Task->execute();
	}
}
