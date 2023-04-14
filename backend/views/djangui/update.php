<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Djangui $model */

$this->title = Yii::t('app', 'Update Djangui: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Djanguis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="djangui-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
