<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use eaBlankonThema\widget\ThAlertList;

/* @var $this yii\web\View */
/* @var $model d4yii2\yii2\wiki\models\Wiki */
/* @var $form yii\widgets\ActiveForm */
/* @var $attachments array */
?>

<div class="row">
    <?= ThAlertList::widget();?>
    <?php $form = ActiveForm::begin(['enableClientValidation'=>false,
        'options' => [
        'enctype' => 'multipart/form-data',
    ]]); ?>
    <div class="col-md-4">
            <h3>Edit Field</h3>
            <div class="form-body">
                <?= $form->field($model, 'id')->textInput(['maxlength' => true, 'class' => 'form-control', 'readonly'=>1]) ?>
                <?= $form->field($model, 'content')->textarea(['rows'=>20, 'class' => 'form-control']) ?>
                <div class="form-group">
                    <?= Html::submitButton('Update', ['class' => 'btn btn-success']) ?>
                </div>
            </div>
    </div>

    <div class="col-md-4">
        <h3>Preview</h3>
        <div id="wiki-preview"><?= nl2br($model->content); ?></div>
    </div>
    <div class="col-md-4">
        <h3>Images (supported *.png, .jpg, .jpeg) (Max 3MB)</h3>
            <label class="form-label" for="customFile">Image upload</label>
            <div class="row">
                <div class="col-md-9">
                    <input type="file" class="form-control" name="Wiki[img]" id="img" />
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <?= Html::submitButton('Upload', ['class' => 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        <?php foreach ($attachments as $a) { ?>
            <div><?=$a['name'];?>
                <a href="<?=Url::to(['/wiki/content/delete-image', 'img' => $a['name'], 'id' => $model->id])?>" data-image="<?=$a['name']?>" class="delete-img actions"><i class="fa fa-trash" aria-hidden="true"></i></a>
                <a href="<?=Url::to(['/wiki/content/show-image', 'img' => $a['name']])?>" class="preview actions" target="_blank"><i class="fa fa-eye" aria-hidden="true"></i></a>
                <a href="javascript:void" data-markup="<img class='img-responsive' src='<?=Url::to(['/wiki/content/show-image', 'img' => $a['name']])?>'>" class="copy-markup actions"><i class="fa fa-files-o" aria-hidden="true"></i></a>
            </div>
        <?php } ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php
$this->registerCss('#wiki-preview {
background:#fff;
color:#000;
font-size:14px;
padding:10px;
border: #D0D0D0 1px solid !important;
}

a.actions {
display:inline-block;
width:20px;
height:20px;
float:right;
margin-left:20px;
}
');

$this->registerJs('
          var content = $("#wiki-content").val();
          $("#wiki-content").keyup(function () {
              if (this.value != content) {
                  content = this.value.replace(/\n/g, "<br />");
                   $("#wiki-preview").html(content);            
               }
          });
          $(".copy-markup").click(function(e){
          e.preventDefault();
          value = $(this).data("markup");
 
          var $temp = $("<input>");
          $("body").append($temp);
          $temp.val(value).select();
          document.execCommand("copy");
          $temp.remove();
          });
') ?>
