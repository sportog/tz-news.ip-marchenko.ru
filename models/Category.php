<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class Category extends ActiveRecord
{
	public static function tableName()
	{
		return 'categories';
	}
	public function getNewsCategory()
	{
		return $this->hasMany(NewsCategory::className(), ['category_id' => 'id']);
	}
	public function getNews()
	{
		return $this->hasMany(News::className(), ['id' => 'news_id'])
			->via('newsCategory');
	}
}
