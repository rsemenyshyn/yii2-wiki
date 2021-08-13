<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model asinfotrack\yii2\wiki\models\Wiki */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="row">
    <div class="col-md-6">
            <?php $form = ActiveForm::begin(['enableClientValidation'=>false]); ?>
            <div class="form-body">
                <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'class' => 'form-control']) ?>
                <?= $form->field($model, 'content')->textarea(['rows'=>20, 'class' => 'form-control']) ?>

                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? 'Create' : 'Update', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>

    </div>

    <div class="col-md-6">
        <div id="wiki-preview"><?= nl2br($model->content); ?></div>
    </div>
</div>

<?php
$this->registerCss('#wiki-preview {
background:#fff;
color:#000;
font-size:14px;
padding:10px;
border: #D0D0D0 1px solid !important;
}');

$this->registerJs('
          var content = $("#wiki-content").val();
          $("#wiki-content").keyup(function () {
              if (this.value != content) {
                  content = this.value.replace(/\n/g, "<br />");
                   $("#wiki-preview").html(content);            
               }
          });
') ?>
