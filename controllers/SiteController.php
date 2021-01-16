<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use yii\web\Response;

class SiteController extends Controller
{
	public function actionError()
	{
		Yii::$app->response->statusCode = 404;
		return [
			'status' => 'error',
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
