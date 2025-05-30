<?php
	return function ($site) {
		$query	 = get('q');
		$results = $site->bettersearch($query, 'title|text')->not('search');
		$results = $results->paginate(10);

		return [
			'query'	 => $query,
			'results' => $results,
			'pagination' => $results->pagination()
		];
	};
?>
