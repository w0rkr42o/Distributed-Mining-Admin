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
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         CakePHP(tm) v 3.0.0
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
namespace Cake\Test\TestCase\Collection;

use Cake\Collection\Collection;
use Cake\TestSuite\TestCase;

/**
 * CollectionTest
 *
 */
class CollectionTest extends TestCase {

/**
 * Tests that it is possible to convert an array into a collection
 *
 * @return void
 */
	public function testArrayIsWrapped() {
		$items = [1, 2, 3];
		$collection = new Collection($items);
		$this->assertEquals($items, iterator_to_array($collection));
	}

/**
 * Tests that it is possible to convert an iterator into a collection
 *
 * @return void
 */
	public function testIteratorIsWrapped() {
		$items = new \ArrayObject([1, 2, 3]);
		$collection = new Collection($items);
		$this->assertEquals(iterator_to_array($items), iterator_to_array($collection));
	}

/**
 * Test running a method over all elements in the collection
 *
 * @return void
 */
	public function testEeach() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->at(0))
			->method('__invoke')
			->with(1, 'a');
		$callable->expects($this->at(1))
			->method('__invoke')
			->with(2, 'b');
		$callable->expects($this->at(2))
			->method('__invoke')
			->with(3, 'c');
		$collection->each($callable);
	}

/**
 * Tests that it is possible to chain filter() as it returns a collection object
 *
 * @return void
 */
	public function testFilterChaining() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->once())
			->method('__invoke')
			->with(3, 'c');
		$filtered = $collection->filter(function ($value, $key, $iterator) {
			return $value > 2;
		});

		$this->assertInstanceOf('\Cake\Collection\Collection', $filtered);
		$filtered->each($callable);
	}

/**
 * Tests reject
 *
 * @return void
 */
	public function testReject() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$result = $collection->reject(function ($v, $k, $items) use ($collection) {
			$this->assertSame($collection, $items);
			return $v > 2;
		});
		$this->assertEquals(['a' => 1, 'b' => 2], iterator_to_array($result));
		$this->assertInstanceOf('\Cake\Collection\Collection', $result);
	}

/**
 * Tests every when the callback returns true for all elements
 *
 * @return void
 */
	public function testEveryReturnTrue() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->at(0))
			->method('__invoke')
			->with(1, 'a')
			->will($this->returnValue(true));
		$callable->expects($this->at(1))
			->method('__invoke')
			->with(2, 'b')
			->will($this->returnValue(true));
		$callable->expects($this->at(2))
			->method('__invoke')
			->with(3, 'c')
			->will($this->returnValue(true));
		$this->assertTrue($collection->every($callable));
	}

/**
 * Tests every when the callback returns false for one of the elements
 *
 * @return void
 */
	public function testEveryReturnFalse() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->at(0))
			->method('__invoke')
			->with(1, 'a')
			->will($this->returnValue(true));
		$callable->expects($this->at(1))
			->method('__invoke')
			->with(2, 'b')
			->will($this->returnValue(false));
		$callable->expects($this->exactly(2))->method('__invoke');
		$this->assertFalse($collection->every($callable));
	}

/**
 * Tests some() when one of the calls return true
 *
 * @return void
 */
	public function testSomeReturnTrue() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->at(0))
			->method('__invoke')
			->with(1, 'a')
			->will($this->returnValue(false));
		$callable->expects($this->at(1))
			->method('__invoke')
			->with(2, 'b')
			->will($this->returnValue(true));
		$callable->expects($this->exactly(2))->method('__invoke');
		$this->assertTrue($collection->some($callable));
	}

/**
 * Tests some() when none of the calls return true
 *
 * @return void
 */
	public function testSomeReturnFalse() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->at(0))
			->method('__invoke')
			->with(1, 'a')
			->will($this->returnValue(false));
		$callable->expects($this->at(1))
			->method('__invoke')
			->with(2, 'b')
			->will($this->returnValue(false));
		$callable->expects($this->at(2))
			->method('__invoke')
			->with(3, 'c')
			->will($this->returnValue(false));
		$this->assertFalse($collection->some($callable));
	}

/**
 * Tests contains
 *
 * @return void
 */
	public function testContains() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$this->assertTrue($collection->contains(2));
		$this->assertTrue($collection->contains(1));
		$this->assertFalse($collection->contains(10));
		$this->assertFalse($collection->contains('2'));
	}

/**
 * Tests map
 *
 * @return void
 */
	public function testMap() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$map = $collection->map(function($v, $k, $it) use ($collection) {
			$this->assertSame($collection, $it);
			return $v * $v;
		});
		$this->assertInstanceOf('\Cake\Collection\Iterator\ReplaceIterator', $map);
		$this->assertEquals(['a' => 1, 'b' => 4, 'c' => 9], iterator_to_array($map));
	}

/**
 * Tests reduce
 *
 * @return void
 */
	public function testReduce() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->at(0))
			->method('__invoke')
			->with(10, 1, 'a')
			->will($this->returnValue(11));
		$callable->expects($this->at(1))
			->method('__invoke')
			->with(11, 2, 'b')
			->will($this->returnValue(13));
		$callable->expects($this->at(2))
			->method('__invoke')
			->with(13, 3, 'c')
			->will($this->returnValue(16));
		$this->assertEquals(16, $collection->reduce($callable, 10));
	}

/**
 * Tests extract
 *
 * @return void
 */
	public function testExtract() {
		$items = [['a' => ['b' => ['c' => 1]]], 2];
		$collection = new Collection($items);
		$map = $collection->extract('a.b.c');
		$this->assertInstanceOf('\Cake\Collection\Iterator\ExtractIterator', $map);
		$this->assertEquals([1, null], iterator_to_array($map));
	}

/**
 * Tests sort
 *
 * @return void
 */
	public function testSortString() {
		$items = [
			['a' => ['b' => ['c' => 4]]],
			['a' => ['b' => ['c' => 10]]],
			['a' => ['b' => ['c' => 6]]]
		];
		$collection = new Collection($items);
		$map = $collection->sortBy('a.b.c');
		$this->assertInstanceOf('\Cake\Collection\Collection', $map);
		$expected = [
			2 => ['a' => ['b' => ['c' => 10]]],
			1 => ['a' => ['b' => ['c' => 6]]],
			0 => ['a' => ['b' => ['c' => 4]]],
		];
		$this->assertEquals($expected, iterator_to_array($map));
	}

/**
 * Tests max
 *
 * @return void
 */
	public function testMax() {
		$items = [
			['a' => ['b' => ['c' => 4]]],
			['a' => ['b' => ['c' => 10]]],
			['a' => ['b' => ['c' => 6]]]
		];
		$collection = new Collection($items);
		$this->assertEquals(['a' => ['b' => ['c' => 10]]], $collection->max('a.b.c'));

		$callback = function($e) {
			return sin($e['a']['b']['c']);
		};
		$this->assertEquals(['a' => ['b' => ['c' => 4]]], $collection->max($callback));
	}

/**
 * Tests min
 *
 * @return void
 */
	public function testMin() {
		$items = [
			['a' => ['b' => ['c' => 4]]],
			['a' => ['b' => ['c' => 10]]],
			['a' => ['b' => ['c' => 6]]]
		];
		$collection = new Collection($items);
		$this->assertEquals(['a' => ['b' => ['c' => 4]]], $collection->min('a.b.c'));
	}

/**
 * Tests groupBy
 *
 * @return void
 */
	public function testGroupBy() {
		$items = [
			['id' => 1, 'name' => 'foo', 'parent_id' => 10],
			['id' => 2, 'name' => 'bar', 'parent_id' => 11],
			['id' => 3, 'name' => 'baz', 'parent_id' => 10],
		];
		$collection = new Collection($items);
		$grouped = $collection->groupBy('parent_id');
		$expected = [
			10 => [
				['id' => 1, 'name' => 'foo', 'parent_id' => 10],
				['id' => 3, 'name' => 'baz', 'parent_id' => 10],
			],
			11 => [
				['id' => 2, 'name' => 'bar', 'parent_id' => 11],
			]
		];
		$this->assertEquals($expected, iterator_to_array($grouped));
		$this->assertInstanceOf('\Cake\Collection\Collection', $grouped);

		$grouped = $collection->groupBy(function($element) {
			return $element['parent_id'];
		});
		$this->assertEquals($expected, iterator_to_array($grouped));
	}

/**
 * Tests grouping by a deep key
 *
 * @return void
 */
	public function testGroupByDeepKey() {
		$items = [
			['id' => 1, 'name' => 'foo', 'thing' => ['parent_id' => 10]],
			['id' => 2, 'name' => 'bar', 'thing' => ['parent_id' => 11]],
			['id' => 3, 'name' => 'baz', 'thing' => ['parent_id' => 10]],
		];
		$collection = new Collection($items);
		$grouped = $collection->groupBy('thing.parent_id');
		$expected = [
			10 => [
				['id' => 1, 'name' => 'foo', 'thing' => ['parent_id' => 10]],
				['id' => 3, 'name' => 'baz', 'thing' => ['parent_id' => 10]],
			],
			11 => [
				['id' => 2, 'name' => 'bar', 'thing' => ['parent_id' => 11]],
			]
		];
		$this->assertEquals($expected, iterator_to_array($grouped));
	}

/**
 * Tests indexBy
 *
 * @return void
 */
	public function testIndexBy() {
		$items = [
			['id' => 1, 'name' => 'foo', 'parent_id' => 10],
			['id' => 2, 'name' => 'bar', 'parent_id' => 11],
			['id' => 3, 'name' => 'baz', 'parent_id' => 10],
		];
		$collection = new Collection($items);
		$grouped = $collection->indexBy('id');
		$expected = [
			1 => ['id' => 1, 'name' => 'foo', 'parent_id' => 10],
			3 => ['id' => 3, 'name' => 'baz', 'parent_id' => 10],
			2 => ['id' => 2, 'name' => 'bar', 'parent_id' => 11],
		];
		$this->assertEquals($expected, iterator_to_array($grouped));
		$this->assertInstanceOf('\Cake\Collection\Collection', $grouped);

		$grouped = $collection->indexBy(function($element) {
			return $element['id'];
		});
		$this->assertEquals($expected, iterator_to_array($grouped));
	}

/**
 * Tests indexBy with a deep property
 *
 * @return void
 */
	public function testIndexByDeep() {
		$items = [
			['id' => 1, 'name' => 'foo', 'thing' => ['parent_id' => 10]],
			['id' => 2, 'name' => 'bar', 'thing' => ['parent_id' => 11]],
			['id' => 3, 'name' => 'baz', 'thing' => ['parent_id' => 10]],
		];
		$collection = new Collection($items);
		$grouped = $collection->indexBy('thing.parent_id');
		$expected = [
			10 => ['id' => 3, 'name' => 'baz', 'thing' => ['parent_id' => 10]],
			11 => ['id' => 2, 'name' => 'bar', 'thing' => ['parent_id' => 11]],
		];
		$this->assertEquals($expected, iterator_to_array($grouped));
	}

/**
 * Tests countBy
 *
 * @return void
 */
	public function testCountBy() {
		$items = [
			['id' => 1, 'name' => 'foo', 'parent_id' => 10],
			['id' => 2, 'name' => 'bar', 'parent_id' => 11],
			['id' => 3, 'name' => 'baz', 'parent_id' => 10],
		];
		$collection = new Collection($items);
		$grouped = $collection->countBy('parent_id');
		$expected = [
			10 => 2,
			11 => 1
		];
		$this->assertEquals($expected, iterator_to_array($grouped));
		$this->assertInstanceOf('\Cake\Collection\Collection', $grouped);

		$grouped = $collection->countBy(function($element) {
			return $element['parent_id'];
		});
		$this->assertEquals($expected, iterator_to_array($grouped));
	}

/**
 * Tests shuffle
 *
 * @return void
 */
	public function testShuffle() {
		$data = [1, 2, 3, 4];
		$collection = (new Collection($data))->shuffle();
		$this->assertEquals(count($data), count(iterator_to_array($collection)));

		foreach ($collection as $value) {
			$this->assertContains($value, $data);
		}
	}

/**
 * Tests sample
 *
 * @return void
 */
	public function testSample() {
		$data = [1, 2, 3, 4];
		$collection = (new Collection($data))->sample(2);
		$this->assertEquals(2, count(iterator_to_array($collection)));

		foreach ($collection as $value) {
			$this->assertContains($value, $data);
		}
	}

/**
 * Test toArray method
 *
 * @return void
 */
	public function testToArray() {
		$data = [1, 2, 3, 4];
		$collection = new Collection($data);
		$this->assertEquals($data, $collection->toArray());
	}

/**
 * Test json enconding
 *
 * @return void
 */
	public function testToJson() {
		$data = [1, 2, 3, 4];
		$collection = new Collection($data);
		$this->assertEquals(json_encode($data), json_encode($collection));
	}

/**
 * Tests that only arrays and Traversables are allowed in the constructor
 *
 * @expectedException \InvalidArgumentException
 * @expectedExceptionMessage Only array or \Traversable are allowed for Collection
 * @return void
 */
	public function testInvalidConstructorArgument() {
		new Collection('Derp');
	}

/**
 * Tests take method
 *
 * @return void
 */
	public function testTake() {
		$data = [1, 2, 3, 4];
		$collection = new Collection($data);

		$taken = $collection->take(2);
		$this->assertEquals([1, 2], $taken->toArray());

		$taken = $collection->take(3);
		$this->assertEquals([1, 2, 3], $taken->toArray());

		$taken = $collection->take(500);
		$this->assertEquals([1, 2, 3, 4], $taken->toArray());

		$taken = $collection->take(1);
		$this->assertEquals([1], $taken->toArray());

		$taken = $collection->take();
		$this->assertEquals([1], $taken->toArray());

		$taken = $collection->take(2, 2);
		$this->assertEquals([2 => 3, 3 => 4], $taken->toArray());
	}

/**
 * Tests match
 *
 * @return void
 */
	public function testMatch() {
		$items = [
			['id' => 1, 'name' => 'foo', 'thing' => ['parent_id' => 10]],
			['id' => 2, 'name' => 'bar', 'thing' => ['parent_id' => 11]],
			['id' => 3, 'name' => 'baz', 'thing' => ['parent_id' => 10]],
		];
		$collection = new Collection($items);
		$matched = $collection->match(['thing.parent_id' => 10, 'name' => 'baz']);
		$this->assertEquals([2 => $items[2]], $matched->toArray());

		$matched = $collection->match(['thing.parent_id' => 10]);
		$this->assertEquals(
			[0 => $items[0], 2 => $items[2]],
			$matched->toArray()
		);

		$matched = $collection->match(['thing.parent_id' => 500]);
		$this->assertEquals([], $matched->toArray());

		$matched = $collection->match(['parent_id' => 10, 'name' => 'baz']);
		$this->assertEquals([], $matched->toArray());
	}

/**
 * Tests firstMatch
 *
 * @return void
 */
	public function testFirstMatch() {
		$items = [
			['id' => 1, 'name' => 'foo', 'thing' => ['parent_id' => 10]],
			['id' => 2, 'name' => 'bar', 'thing' => ['parent_id' => 11]],
			['id' => 3, 'name' => 'baz', 'thing' => ['parent_id' => 10]],
		];
		$collection = new Collection($items);
		$matched = $collection->firstMatch(['thing.parent_id' => 10]);
		$this->assertEquals(
			['id' => 1, 'name' => 'foo', 'thing' => ['parent_id' => 10]],
			$matched
		);

		$matched = $collection->firstMatch(['thing.parent_id' => 10, 'name' => 'baz']);
		$this->assertEquals(
			['id' => 3, 'name' => 'baz', 'thing' => ['parent_id' => 10]],
			$matched
		);
	}

/**
 * Tests the append method
 *
 * @return void
 */
	public function testAppend() {
		$collection = new Collection([1, 2, 3]);
		$combined = $collection->append([4, 5, 6]);
		$this->assertEquals([1, 2, 3, 4, 5, 6], $combined->toArray(false));

		$collection = new Collection(['a' => 1, 'b' => 2]);
		$combined = $collection->append(['c' => 3, 'a' => 4]);
		$this->assertEquals(['a' => 4, 'b' => 2, 'c' => 3], $combined->toArray());
	}

/**
 * Tests that by calling compile internal iteration operations are not done
 * more than once
 *
 * @return void
 */
	public function testCompile() {
		$items = ['a' => 1, 'b' => 2, 'c' => 3];
		$collection = new Collection($items);
		$callable = $this->getMock('stdClass', ['__invoke']);
		$callable->expects($this->at(0))
			->method('__invoke')
			->with(1, 'a')
			->will($this->returnValue(4));
		$callable->expects($this->at(1))
			->method('__invoke')
			->with(2, 'b')
			->will($this->returnValue(5));
		$callable->expects($this->at(2))
			->method('__invoke')
			->with(3, 'c')
			->will($this->returnValue(6));
		$compiled = $collection->map($callable)->compile();
		$this->assertEquals(['a' => 4, 'b' => 5, 'c' => 6], $compiled->toArray());
		$this->assertEquals(['a' => 4, 'b' => 5, 'c' => 6], $compiled->toArray());
	}

}
