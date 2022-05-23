<?php
use yii\bootstrap\Button;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model d4yii2\yii2\wiki\models\Wiki */

$this->title = Html::encode($model->title);

?>

<div class="wiki-article">
    <h1><?= Html::encode($this->title) ?></h1>
    <div class="wiki-content">
        <?= \Parsedown::instance()->parse($model->content) ?>
    </div>
</div>

<?php if (Yii::$app->user->can(\d4yii2\yii2\wiki\accessRights\WikiEditUserRole::NAME)) { ?>
    <?= Button::widget([
        'tagName'=>'a',
        'label'=>Yii::t('app', 'Update'),
        'options'=>[
            'class'=>'btn-primary',
            'href'=>Url::to(['update', 'id'=>$model->id]),
        ],
    ]) ?>
<?php } ?>
