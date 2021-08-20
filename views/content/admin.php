<?php
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $searchModel d4yii2\yii2\wiki\models\WikiSearch */
?>

<?= GridView::widget([
	'dataProvider'=>$dataProvider,
	'filterModel'=>$searchModel,
	'columns'=>[
		'id',
		'isOrphan:boolean',
		'content:ntext',
	],
]) ?>
