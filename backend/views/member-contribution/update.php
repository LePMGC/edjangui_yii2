<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\DjanguiContribution */

$this->title = Yii::t('app', 'Update Djangui Contribution: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Djangui Contributions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="djangui-contribution-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
