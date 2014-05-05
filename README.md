# Paginate

[![Build Status](https://travis-ci.org/volnix/paginate.png?branch=master)](https://travis-ci.org/volnix/paginate) [![Total Downloads](https://poser.pugx.org/volnix/paginate/downloads.png)](https://packagist.org/packages/volnix/paginate) [![Latest Stable Version](https://poser.pugx.org/volnix/paginate/v/stable.png)](https://packagist.org/packages/volnix/paginate)

Paginate is a dead-simple pagination data-model.  It holds metadata about your pagination system and also the current state of pagination at any given time.  It is useful for use in models for server-side pagination, views for client-side pagination building, and everywhere in between.

## Basic Usage

The pagination library can be used with zero configuration if desired.  There are 3 config settings for Paginate and are simply metadata about your pagination system:

- Results per Page (default: 50) - the number of results to display on each page
- Page Range (default: 2) - the number of pages on either side of the current page to display (e.g., if you're on page 5 it would show pages 3 through 7 in your pagination)
- Index (default: 'page') - the GET/POST index that your current page identifier is passed in on; useful when it's time to start building up pagination

Zero-configuration pagination:

```php

// instantiate our pager
$pager = new \Volnix\Paginate\Pager;

// actually set the pagination parameters
$pager->paginate(1000, 5); // 100 results in result-set, current page = 5

```

> **Note:** The `paginate` method is what actually sets the current pagination state.  This must be called with the result count (and optionally current page) to build up useful pagination information.

The use-case above would set the following parameters about the current state of your pagination (these correspond to class variables in the Pager):

- Number of pages ($max_page): 20 (1000 results, 50 per page)
- Current page: 5
- Page range shown ($low_page - $high_page): 3 - 7 (2 page page-range)
- Query skip ($query_skip): 400 (on page 5 with 100 results per page, so skip the first 400 since we're on 401-500)

## Configuring Pagination

You may also set the style of pagination you want by passing in metadata through the constructor or calling the `setConfig` method:

```php

// both of the below examples do the exact same thing
$pager = new \Volnix\Paginate\Pager(5, 5, 'foo');

$pager = new \Volnix\Paginate\Pager;
$pager->setConfig(5, 5, 'foo');

```

> **Note:** Neither of the above examples are calling the `paginate` method.  The pager is not of any value until `paginate` is called, for that method actually sets your current state.

## Pagination Information

The class variables exposed by the Pager are the core of the information provided by this library and are what are to be used by any extensions:

- `paginate` - A bool of whether to paginate or not
- `current_page` - The current page (passed in through the `paginate` method call.
- `query_skip` - The amount of records of skip in your ORM when querying for the next result-set
- `result_count` - The total number of results matching a given set of search criteria that need to be paged through
- `min_page` - The minimum page of pagination (always 1)
- `max_page` - The maximum page of pagination (equals the number of results / the results per page)
- `low_page` - The low range of pages to display (determined by `$page_range`)
- `high_page` - The high range of pages to display

```php

public $paginate				= true; // whether to even paginate or not
public $current_page			= 1; // current page
public $query_skip				= 0; // the skip step to be passed into queries
public $result_count			= 0; // how many results we're paginating
public $min_page				= 1; // min page of all pages
public $max_page				= 1; // max page (i.e. results / results per page)
public $low_page				= 1; // low page in range of pages being displayed
public $high_page				= 1; // high page in range of pages being displayed

```

## Method Chaining

The `paginate` and `setConfig` methods both return the Pager object, so method-chaining is possible:

```php

$pager = new \Volnix\Paginate\Pager;
$pager->setConfig(5, 5, 'foo')->paginate(100);

// OR

$pager = (new \Volnix\Paginate\Pager)->setConfig(5, 5, 'foo')->paginate(100);

```

## Extending Paginate

This library is meant to be extended.  Once you have your basic pagination information, it is possible to build up server-side, client-side, etc. pagination in whatever ORM or javascript library you use in your project.  I have provided a simple example in the class of pagination HTML that is rendered, but the possibilities are endless:

In your model/controller/wherever:

```php

$pager = (new \Volnix\Paginate\Pager)->paginate(1000, 5);

```

In your view:

```php

<div id="pagination-block">
	<?= $pager->getHTMLPagination('pagination', $base_url) ?>
</div>

```