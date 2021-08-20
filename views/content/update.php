<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model d4yii2\yii2\wiki\models\Wiki */
/* @var $attachments array */

$this->title = 'Update Wiki: ' . ' ' . $model->title;
?>

<h1><?= Html::encode($this->title) ?></h1>
<?= $this->render('partials/_form', [
	'model' => $model,
    'attachments' => $attachments
]) ?>
