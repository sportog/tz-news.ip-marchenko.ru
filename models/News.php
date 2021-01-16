<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class News extends ActiveRecord
{
	public static function tableName()
	{
		return 'news';
	}
	public function getNewsCategory()
	{
		return $this->hasMany(NewsCategory::className(), ['news_id' => 'id']);
	}
	public function getCategories()
	{
		return $this->hasMany(Category::className(), ['id' => 'category_id'])
			->via('newsCategory');
	}
}
