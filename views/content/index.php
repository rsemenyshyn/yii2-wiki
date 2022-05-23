<?php
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel d4yii2\yii2\wiki\models\WikiSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Wikis';
?>

<h1><?= Html::encode($this->title) ?></h1>

<?php if (Yii::$app->user->can(\d4yii2\yii2\wiki\accessRights\WikiEditUserRole::NAME)) { ?>
    <p><?= Html::a('Create Wiki', ['create'], ['class' => 'btn btn-success']) ?></p>
<?php } ?>

<?= GridView::widget([
	'dataProvider' => $dataProvider,
	'filterModel' => $searchModel,
	'columns' => [
		['class' => 'yii\grid\SerialColumn'],

		'id',
		'title',
		'content:ntext',
		'created',
		'created_by',
		'updated',
		'updated_by',

		['class' => 'yii\grid\ActionColumn'],
	],
]); ?>
