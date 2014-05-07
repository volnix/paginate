<?php

namespace Volnix\Paginate;

/**
 * A simple pager that is more or less a dump of information about your pagination.  This is meant to be easily extensible for custom pagination solutions.
 */
class Pager {

	/*
	 * These are hard-coded defaults to use in the case that none are passed in
	 */
	const DEFAULT_RESULTS_PER_PAGE	= 50;
	const DEFAULT_PAGE_RANGE		= 2;
	const DEFAULT_INDEX				= 'page';

	/*
	 * The meta-data about the pager
	 */
	public $results_per_page		= 50; // how many results to display per page
	public $page_range				= 2; // how many pages either side of the current page to show pagination for (i.e. current page = 10, so show pages 8 - 12)
	public $index					= 'page'; // page number index

	/*
	 * The data about the actual state that the pager is in
	 */
	public $paginate				= true; // whether to even paginate or not
	public $current_page			= 1; // current page
	public $query_skip				= 0; // the skip step to be passed into queries
	public $result_count			= 0; // how many results we're paginating
	public $min_page				= 1; // min page of all pages
	public $max_page				= 1; // max page (i.e. results / results per page)
	public $low_page				= 1; // low page in range of pages being displayed
	public $high_page				= 1; // high page in range of pages being displayed

	/**
	 * Constructor
	 *
	 * @param int $results_per_page The number of results to display per page
	 * @param int $page_range The range either way of the current page to show
	 * @param string $index The name of the page index passed in throw POST/GET
	 */
	public function __construct($results_per_page = self::DEFAULT_RESULTS_PER_PAGE, $page_range = self::DEFAULT_PAGE_RANGE, $index = self::DEFAULT_INDEX)
	{
		$this->setConfig($results_per_page, $page_range, $index);
	}

	/**
	 * Actually set the current pagination state
	 *
	 * @param type $result_count Number of results to paginate for
	 * @param type $current_page The current page you are on
	 * @return \Volnix\Paginate\Pager
	 * @throws \LogicException
	 */
	public function paginate($result_count, $current_page = 1)
	{
		$this->current_page = empty($current_page) ? 1 : $current_page;
		$this->result_count = $result_count;

		if ($this->result_count <= $this->results_per_page) {
			$this->paginate = false;
		} else {
			$this->paginate = true;
			$this->query_skip = ($this->current_page - 1) * $this->results_per_page;
			$this->min_page = 1;
			$this->max_page = (int)($this->result_count / $this->results_per_page) + (($this->result_count % $this->results_per_page) > 0 ? 1 : 0);
			$this->low_page = ($this->current_page - $this->page_range) >= $this->min_page ? ($this->current_page - $this->page_range) : $this->min_page;
			$this->high_page = ($this->current_page + $this->page_range) <= $this->max_page ? ($this->current_page + $this->page_range) : $this->max_page;
		}

		if ($this->current_page > $this->max_page) {
			throw new \LogicException("Current page is larger than max page.");
		}

		return $this;
	}

	/**
	 * Change the results per page, page range, and index name if desired
	 *
	 * @param int $results_per_page The number of results to display per page
	 * @param int $page_range The range either way of the current page to show
	 * @param string $index The name of the page index passed in throw POST/GET
	 * @return \Volnix\Paginate\Pager
	 */
	public function setConfig($results_per_page = self::DEFAULT_RESULTS_PER_PAGE, $page_range = self::DEFAULT_PAGE_RANGE, $index = self::DEFAULT_INDEX)
	{
		$this->results_per_page = $results_per_page ?: self::DEFAULT_RESULTS_PER_PAGE;
		$this->index = $index ?: self::DEFAULT_INDEX;
		$this->page_range = $page_range ?: self::DEFAULT_PAGE_RANGE;

		return $this;
	}

	/**
	 * Get all the pertinent data in array form.  Useful for API pagination, etc.
	 *
	 * Returns current page, previous page, next page, query skip, minimum page, maximum page.
	 *
	 * @return array
	 */
	public function toArray()
	{
		$data = [
			'current' => $this->current_page,
			'prev' => ($this->current_page - 1 >= $this->min_page ? $this->current_page - 1 : $this->min_page),
			'next' => ($this->current_page + 1 <= $this->max_page ? $this->current_page + 1 : $this->max_page),
			'skip' => $this->query_skip,
			'min' => $this->min_page,
			'max' => $this->max_page
		];

		return $data;
	}

	/**
	 * Returns a json version of the toArray function.
	 *
	 * @return string
	 */
	public function toJson()
	{
		return json_encode($this->toArray());
	}

	public function __toString() {
		return $this->toJson();
	}
}
