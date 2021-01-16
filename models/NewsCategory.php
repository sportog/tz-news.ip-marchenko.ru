<?php

namespace app\models;

use Yii;
use yii\db\ActiveRecord;

class NewsCategory extends ActiveRecord
{
	public static function tableName()
	{
		return 'news_category';
	}
}
