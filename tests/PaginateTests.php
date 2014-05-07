<?php namespace Volnix\Paginate\Tests;

class PaginateTests extends \PHPUnit_Framework_TestCase {

	public function testUseDefaults()
	{
		$pager = new \Volnix\Paginate\Pager;
		$this->assertEquals(50, $pager->results_per_page);
		$this->assertEquals(2, $pager->page_range);
		$this->assertEquals('page', $pager->index);
	}

	public function testSimplePagination()
	{
		$pager = (new \Volnix\Paginate\Pager)->paginate(100);
		$this->assertEquals(1, $pager->current_page);
		$this->assertEquals(0, $pager->query_skip);
		$this->assertTrue($pager->paginate);
		$this->assertEquals(1, $pager->min_page);
		$this->assertEquals(2, $pager->max_page);
		$this->assertEquals(1, $pager->low_page);
		$this->assertEquals(2, $pager->high_page);
	}

	public function testCurrentPagePagination()
	{
		$pager = (new \Volnix\Paginate\Pager)->paginate(100, 2);
		$this->assertEquals(2, $pager->current_page);
		$this->assertEquals(50, $pager->query_skip);
		$this->assertTrue($pager->paginate);
		$this->assertEquals(1, $pager->min_page);
		$this->assertEquals(2, $pager->max_page);
		$this->assertEquals(1, $pager->low_page);
		$this->assertEquals(2, $pager->high_page);
	}

	public function testCurrentPageLargeRange()
	{
		$pager = (new \Volnix\Paginate\Pager)->paginate(1000, 4);
		$this->assertEquals(4, $pager->current_page);
		$this->assertEquals(150, $pager->query_skip);
		$this->assertTrue($pager->paginate);
		$this->assertEquals(1, $pager->min_page);
		$this->assertEquals(20, $pager->max_page);
		$this->assertEquals(2, $pager->low_page);
		$this->assertEquals(6, $pager->high_page);
	}

	public function testSmallRange()
	{
		$pager = (new \Volnix\Paginate\Pager)->paginate(10);
		$this->assertEquals(1, $pager->current_page);
		$this->assertEquals(0, $pager->query_skip);
		$this->assertFalse($pager->paginate);
		$this->assertEquals(1, $pager->min_page);
		$this->assertEquals(1, $pager->max_page);
		$this->assertEquals(1, $pager->low_page);
		$this->assertEquals(1, $pager->high_page);
	}

	public function testCurrentPageEndOfRange()
	{
		$pager = (new \Volnix\Paginate\Pager)->paginate(1000, 20);
		$this->assertEquals(20, $pager->current_page);
		$this->assertEquals(1, $pager->min_page);
		$this->assertEquals(20, $pager->max_page);
		$this->assertEquals(18, $pager->low_page);
		$this->assertEquals(20, $pager->high_page);

		unset($pager);
		$pager = (new \Volnix\Paginate\Pager)->paginate(1000, 1);
		$this->assertEquals(1, $pager->current_page);
		$this->assertEquals(1, $pager->min_page);
		$this->assertEquals(20, $pager->max_page);
		$this->assertEquals(1, $pager->low_page);
		$this->assertEquals(3, $pager->high_page);
	}

	public function testSetConfigPaginate()
	{
		$pager = new \Volnix\Paginate\Pager(5, 5, 'foo');
		$this->assertEquals(5, $pager->results_per_page);
		$this->assertEquals(5, $pager->page_range);
		$this->assertEquals('foo', $pager->index);

		$pager->paginate(100, 10);
		$this->assertEquals(10, $pager->current_page);
		$this->assertEquals(45, $pager->query_skip);
		$this->assertTrue($pager->paginate);
		$this->assertEquals(1, $pager->min_page);
		$this->assertEquals(20, $pager->max_page);
		$this->assertEquals(5, $pager->low_page);
		$this->assertEquals(15, $pager->high_page);

		$pager = (new \Volnix\Paginate\Pager)->setConfig(10, 3, 'baz')->paginate(100, 10);
		$this->assertEquals(10, $pager->results_per_page);
		$this->assertEquals(3, $pager->page_range);
		$this->assertEquals('baz', $pager->index);

		$pager->paginate(100, 5);
		$this->assertEquals(5, $pager->current_page);
		$this->assertEquals(40, $pager->query_skip);
		$this->assertTrue($pager->paginate);
		$this->assertEquals(1, $pager->min_page);
		$this->assertEquals(10, $pager->max_page);
		$this->assertEquals(2, $pager->low_page);
		$this->assertEquals(8, $pager->high_page);
	}

	/**
     * @expectedException LogicException
     */
	public function testInvalidCurrentPageException()
	{
		$pager = (new \Volnix\Paginate\Pager)->paginate(10, 2);
	}

	public function testGetDataAsArray()
	{
		$data = (new \Volnix\Paginate\Pager)->paginate(10)->toArray();
		$this->assertTrue(is_array($data));
		$this->assertEquals($data['prev'], 1);
		$this->assertEquals($data['next'], 1);
		$this->assertEquals($data['skip'], 0);
		$this->assertEquals($data['min'], 1);
		$this->assertEquals($data['max'], 1);

		$data = (new \Volnix\Paginate\Pager)->paginate(100, 2)->toArray();
		$this->assertTrue(is_array($data));
		$this->assertEquals($data['prev'], 1);
		$this->assertEquals($data['next'], 2);
		$this->assertEquals($data['skip'], 50);
		$this->assertEquals($data['min'], 1);
		$this->assertEquals($data['max'], 2);
	}

	public function testGetDataAsJson()
	{
		$json = (new \Volnix\Paginate\Pager)->paginate(10)->toJson();
		$this->assertTrue(is_string($json));

		$data = json_decode($json);
		$this->assertTrue(is_object($data));
		$this->assertEquals($data->prev, 1);
		$this->assertEquals($data->next, 1);
		$this->assertEquals($data->skip, 0);
		$this->assertEquals($data->min, 1);
		$this->assertEquals($data->max, 1);

		ob_start();
		print (new \Volnix\Paginate\Pager)->paginate(10);
		$json = ob_get_clean();

		$this->assertTrue(is_string($json));

		$data = json_decode($json);
		$this->assertTrue(is_object($data));
		$this->assertEquals($data->prev, 1);
		$this->assertEquals($data->next, 1);
		$this->assertEquals($data->skip, 0);
		$this->assertEquals($data->min, 1);
		$this->assertEquals($data->max, 1);
	}
}