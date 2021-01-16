<?php

namespace app\controllers;

use app\models\News;
use app\models\Category;
use Yii;
use yii\rest\Controller;
use yii\web\Response;

class NewsController extends Controller
{
	private function treeCategory(array $data, int $category_id)
	{
		$return = [$category_id];
		if (isset($data[$category_id]) && count($data[$category_id])) {
			foreach ($data[$category_id] as $category_sub_id) {
				$return = array_merge($return, $this->treeCategory($data, $category_sub_id));
			}
		}
		return $return;
	}
	// Список категорий
	public function actionCategories()
	{
		$categories = Category::find()
			->select(['id', 'title', 'category_id_parent'])
			->orderBy(['category_id_parent' => SORT_ASC])
			->asArray()
			->all();
		$cacheCategoriesParent = [];
		foreach ($categories as $category) {
			$cacheCategoriesParent[$category['category_id_parent']][] = $category['id'];
		}
		Yii::$app->redis->set('cacheCategoriesParent', json_encode($cacheCategoriesParent));
		return $categories;
	}
	// Новости в категории
	public function actionCategory($category_id)
	{
		if (!$category = Category::findOne($category_id)) {
			Yii::$app->response->statusCode = 404;
			return [
				'status' => 'error',
			];
		}
		$cacheCategoriesParent = json_decode(Yii::$app->redis->get('cacheCategoriesParent'), true);
		$whereCategories = $this->treeCategory($cacheCategoriesParent, $category->id);
		$dbNews = News::find()
			->select(['news.id', 'news.title'])
			->joinWith(['newsCategory'])
			->where(['category_id' => $whereCategories])
			->orderBy(['news_id' => SORT_ASC])
			->asArray()
			->all();
		return $dbNews;
	}
	// Просмотр новости
	public function actionNews($news_id)
	{
		if (!$news = News::findOne($news_id)) {
			Yii::$app->response->statusCode = 404;
			return [
				'status' => 'error',
			];
		}
		$news->updateCounters(['count_reads' => 1]);

		$redisResponse = [
			'type' => 'COUNT_READS',
			'data' => [
				'id' => $news->id,
				'count_reads' => $news->count_reads,
			]
		];
		Yii::$app->redis->executeCommand('publish', ['system', json_encode($redisResponse)]);

		$newsCategories = $news->categories;
		$categories = [];
		if (count($newsCategories)) {
			foreach ($newsCategories as $newsCategory) {
				$categories[] = $newsCategory->id;
			}
		}
		return [
			'id' => $news->id,
			'title' => $news->title,
			'content' => $news->content,
			'count_reads' => $news->count_reads,
			'categories' => $categories,
		];
	}
	public function beforeAction($action)
	{
		if (!parent::beforeAction($action))
			return false;
		Yii::$app->response->format = Response::FORMAT_JSON;
		return true;
	}
}
