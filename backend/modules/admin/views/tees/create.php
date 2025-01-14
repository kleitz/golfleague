<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Tees */

$this->title = Yii::t('igolf', 'Create Tees');
$this->params['breadcrumbs'][] = ['label' => $model->course->name, 'url' => ['course/view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tees-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
