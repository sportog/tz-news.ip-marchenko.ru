<?php

use yii\db\Migration;

class m210116_040306_tznews extends Migration
{
	public function up()
	{
		// Добавляем таблицы
		$this->createTable('categories', [
			'id' => $this->primaryKey()
				->unsigned(),
			'category_id_parent' => $this->integer()
				->unsigned(),
			'title' => $this->string(50)->notNull(),
		]);
		$this->createTable('news', [
			'id' => $this->primaryKey()
				->unsigned(),
			'title' => $this->string(150)->notNull(),
			'content' => $this->text(),
			'count_reads' => $this->integer()
				->notNull()
				->defaultValue(0)
				->unsigned(),
		]);
		$this->createTable('news_category', [
			'id' => $this->primaryKey()
				->unsigned(),
			'news_id' => $this->integer()->notNull()
				->unsigned(),
			'category_id' => $this->integer()->notNull()
				->unsigned(),
		]);
		// Добавляем ключи
		$this->createIndex(
			'idx-categories-category_id_parent',
			'categories',
			'category_id_parent'
		);
		$this->addForeignKey(
			'fk-categories-category_id_parent',
			'categories',
			'category_id_parent',
			'categories',
			'id',
			'CASCADE'
		);
		$this->createIndex(
			'idx-news-category-category_id',
			'news_category',
			'category_id'
		);
		$this->addForeignKey(
			'fk-news-category-category_id',
			'news_category',
			'category_id',
			'categories',
			'id',
			'CASCADE'
		);
		$this->createIndex(
			'idx-news-category-news_id',
			'news_category',
			'news_id'
		);
		$this->addForeignKey(
			'fk-news-category-news_id',
			'news_category',
			'news_id',
			'news',
			'id',
			'CASCADE'
		);
		// Добавляем данные
		$data = [
			[
				'title' => 'Общество',
				'categories' => [[
					'title' => 'городская жизнь',
					'news' => [
						[
							'title' => 'Тестовый заголовок городская жизнь 1',
							'content' => 'Содержимое тестовой новости городская жизнь 1. Содержимое тестовой новости городская жизнь 1.'
						],
						[
							'title' => 'Тестовый заголовок городская жизнь 2',
							'content' => 'Содержимое тестовой новости городская жизнь 2. Содержимое тестовой новости городская жизнь 2. Содержимое тестовой новости городская жизнь 2. Содержимое тестовой новости городская жизнь 2.'
						],
						[
							'title' => 'Тестовый заголовок городская жизнь 3',
							'content' => 'Содержимое тестовой новости городская жизнь 3. Содержимое тестовой новости городская жизнь 3. Содержимое тестовой новости городская жизнь 3. Содержимое тестовой новости городская жизнь 3. Содержимое тестовой новости городская жизнь 3. Содержимое тестовой новости городская жизнь 3.'
						],
					],
				], [
					'title' => 'выборы',
					'news' => [],
				]],
				'news' => [
					[
						'title' => 'Тестовый заголовок Общество 1',
						'content' => 'Содержимое тестовой новости Общество 1. Содержимое тестовой новости Общество 1.'
					],
					[
						'title' => 'Тестовый заголовок Общество 2',
						'content' => 'Содержимое тестовой новости Общество 2. Содержимое тестовой новости Общество 2. Содержимое тестовой новости Общество 2. Содержимое тестовой новости Общество 2.'
					],
				],
			],
			[
				'title' => 'День города',
				'categories' => [[
					'title' => 'салюты',
					'news' => [],
				], [
					'title' => 'детская площадка',
					'categories' => [[
						'title' => '0-3 года',
						'news' => [
							[
								'title' => 'Тестовый заголовок 0-3 года 1',
								'content' => 'Содержимое тестовой новости 0-3 года 1. Содержимое тестовой новости 0-3 года 1.'
							],
						],
					], [
						'title' => '3-7 года',
						'news' => [
							[
								'title' => 'Тестовый заголовок 3-7 года 1',
								'content' => 'Содержимое тестовой новости 3-7 года 1. Содержимое тестовой новости 3-7 года 1.'
							],
						],
					]],
					'news' => [],
				]],
				'news' => [
					[
						'title' => 'Тестовый заголовок День города 1',
						'content' => 'Содержимое тестовой новости День города 1. Содержимое тестовой новости День города 1.'
					],
				],
			],
			[
				'title' => 'Спорт',
				'news' => [
					[
						'title' => 'Тестовый заголовок Спорт 1',
						'content' => 'Содержимое тестовой новости Спорт 1. Содержимое тестовой новости Спорт 1.'
					],
					[
						'title' => 'Тестовый заголовок Спорт 2',
						'content' => 'Содержимое тестовой новости Спорт 2. Содержимое тестовой новости Спорт 2. Содержимое тестовой новости Спорт 2. Содержимое тестовой новости Спорт 2.'
					],
					[
						'title' => 'Тестовый заголовок Спорт 3',
						'content' => 'Содержимое тестовой новости Спорт 3. Содержимое тестовой новости Спорт 3. Содержимое тестовой новости Спорт 3. Содержимое тестовой новости Спорт 3. Содержимое тестовой новости Спорт 3. Содержимое тестовой новости Спорт 3.'
					],
				],
			]
		];
		$categoryId = $this->categoryToDB($data, 0);
		// Связь с категорией (для первой новости с крайне категорией для демонстрации более одной привязанной категории к новости)
		$this->insert('news_category', [
			'category_id' => $categoryId,
			'news_id' => 1,
		]);
	}

	public function down()
	{
		// Удаляем ключи
		$this->dropForeignKey(
			'fk-categories-category_id_parent',
			'categories'
		);
		$this->dropIndex(
			'idx-categories-category_id_parent',
			'categories'
		);
		$this->dropForeignKey(
			'fk-news-category-category_id',
			'news_category'
		);
		$this->dropIndex(
			'idx-news-category-category_id',
			'news_category'
		);
		$this->dropForeignKey(
			'fk-news-category-news_id',
			'news_category'
		);
		$this->dropIndex(
			'idx-news-category-news_id',
			'news_category'
		);
		// Удаляем таблицы
		$this->dropTable('categories');
		$this->dropTable('news');
		$this->dropTable('news_category');
	}

	private function categoryToDB(array $data, int $category_id_parent)
	{
		foreach ($data as $category) {
			// Категория
			$dataCategory = [
				'title' => $category['title'],
			];
			if ($category_id_parent)
				$dataCategory['category_id_parent'] = $category_id_parent;
			$this->insert('categories', $dataCategory);
			$categoryId = $this->db->lastInsertID;
			// Новости
			if (isset($category['news']) && count($category['news'])) {
				foreach ($category['news'] as $news) {
					$this->insert('news', $news);
					$newsId = $this->db->lastInsertID;
					// Связь с категорией
					$this->insert('news_category', [
						'category_id' => $categoryId,
						'news_id' => $newsId,
					]);
				}
			}
			// Дочерние категории
			if (isset($category['categories']) && count($category['categories']))
				$this->categoryToDB($category['categories'], $categoryId);
		}
		return $categoryId;
	}
}
