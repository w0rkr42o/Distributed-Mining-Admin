<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://book.cakephp.org/2.0/en/development/testing.html CakePHP(tm) Tests
 * @since         CakePHP(tm) v 2.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace Cake\Test\TestCase\Controller;

use Cake\Controller\ComponentRegistry;
use Cake\Controller\Component\CookieComponent;
use Cake\Controller\Controller;
use Cake\Core\App;
use Cake\Core\Plugin;
use Cake\TestSuite\TestCase;

/**
 * Extended CookieComponent
 */
class CookieAliasComponent extends CookieComponent {
}

class ComponentRegistryTest extends TestCase {

/**
 * setUp
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$controller = new Controller();
		$this->Components = new ComponentRegistry($controller);
	}

/**
 * tearDown
 *
 * @return void
 */
	public function tearDown() {
		parent::tearDown();
		unset($this->Components);
	}

/**
 * test triggering callbacks on loaded helpers
 *
 * @return void
 */
	public function testLoad() {
		$result = $this->Components->load('Cookie');
		$this->assertInstanceOf('Cake\Controller\Component\CookieComponent', $result);
		$this->assertInstanceOf('Cake\Controller\Component\CookieComponent', $this->Components->Cookie);

		$result = $this->Components->loaded();
		$this->assertEquals(array('Cookie'), $result, 'loaded() results are wrong.');

		$result = $this->Components->load('Cookie');
		$this->assertSame($result, $this->Components->Cookie);
	}

/**
 * Tests loading as an alias
 *
 * @return void
 */
	public function testLoadWithAlias() {
		$result = $this->Components->load('Cookie', array('className' => __NAMESPACE__ . '\CookieAliasComponent', 'somesetting' => true));
		$this->assertInstanceOf(__NAMESPACE__ . '\CookieAliasComponent', $result);
		$this->assertInstanceOf(__NAMESPACE__ . '\CookieAliasComponent', $this->Components->Cookie);
		$this->assertTrue($this->Components->Cookie->settings['somesetting']);

		$result = $this->Components->loaded();
		$this->assertEquals(array('Cookie'), $result, 'loaded() results are wrong.');

		$result = $this->Components->load('Cookie');
		$this->assertInstanceOf(__NAMESPACE__ . '\CookieAliasComponent', $result);

		Plugin::load('TestPlugin');
		$result = $this->Components->load('SomeOther', array('className' => 'TestPlugin.Other'));
		$this->assertInstanceOf('TestPlugin\Controller\Component\OtherComponent', $result);
		$this->assertInstanceOf('TestPlugin\Controller\Component\OtherComponent', $this->Components->SomeOther);

		$result = $this->Components->loaded();
		$this->assertEquals(array('Cookie', 'SomeOther'), $result, 'loaded() results are wrong.');
	}

/**
 * test load and enable = false
 *
 * @return void
 */
	public function testLoadWithEnableFalse() {
		$mock = $this->getMock('Cake\Event\EventManager');
		$mock->expects($this->never())
			->method('attach');

		$this->Components->getController()->setEventManager($mock);

		$result = $this->Components->load('Cookie', array('enabled' => false));
		$this->assertInstanceOf('Cake\Controller\Component\CookieComponent', $result);
		$this->assertInstanceOf('Cake\Controller\Component\CookieComponent', $this->Components->Cookie);
	}

/**
 * test missingcomponent exception
 *
 * @expectedException Cake\Error\MissingComponentException
 * @return void
 */
	public function testLoadMissingComponent() {
		$this->Components->load('ThisComponentShouldAlwaysBeMissing');
	}

/**
 * test loading a plugin component.
 *
 * @return void
 */
	public function testLoadPluginComponent() {
		Plugin::load('TestPlugin');
		$result = $this->Components->load('TestPlugin.Other');
		$this->assertInstanceOf('TestPlugin\Controller\Component\OtherComponent', $result, 'Component class is wrong.');
		$this->assertInstanceOf('TestPlugin\Controller\Component\OtherComponent', $this->Components->Other, 'Class is wrong');
	}

/**
 * Test loading components with aliases and plugins.
 *
 * @return void
 */
	public function testLoadWithAliasAndPlugin() {
		Plugin::load('TestPlugin');
		$result = $this->Components->load('AliasedOther', ['className' => 'TestPlugin.Other']);
		$this->assertInstanceOf('TestPlugin\Controller\Component\OtherComponent', $result);
		$this->assertInstanceOf('TestPlugin\Controller\Component\OtherComponent', $this->Components->AliasedOther);

		$result = $this->Components->loaded();
		$this->assertEquals(['AliasedOther'], $result, 'loaded() results are wrong.');
	}

/**
 * test getting the controller out of the collection
 *
 * @return void
 */
	public function testGetController() {
		$result = $this->Components->getController();
		$this->assertInstanceOf('Cake\Controller\Controller', $result);
	}

/**
 * Test reset.
 *
 * @return void
 */
	public function testReset() {
		$eventManager = $this->Components->getController()->getEventManager();
		$instance = $this->Components->load('Auth');
		$this->assertSame(
			$instance,
			$this->Components->Auth,
			'Instance in registry should be the same as previously loaded'
		);
		$this->assertCount(1, $eventManager->listeners('Controller.startup'));

		$this->assertNull($this->Components->reset(), 'No return expected');
		$this->assertCount(0, $eventManager->listeners('Controller.startup'));

		$this->assertNotSame($instance, $this->Components->load('Auth'));
	}

}
