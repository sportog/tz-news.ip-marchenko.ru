<?php

return [
	'/api/categories' => 'news/categories',
	'/api/category/<category_id:\d+>' => 'news/category',
	'/api/news/<news_id:\d+>' => 'news/news',
];
