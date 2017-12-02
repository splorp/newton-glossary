<?php
	return function($site, $pages, $page) {
		$query   = get('q');
		$results = $site->search($query, 'title');
		$results = $results->sortBy('title')->paginate(10);
		return array(
			'query'   => $query,
			'results' => $results,
			'pagination' => $results->pagination()
		);
	};
?>
