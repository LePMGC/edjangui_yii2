<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Season $model */

$this->title = Yii::t('app', 'Update Season: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Seasons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="season-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
