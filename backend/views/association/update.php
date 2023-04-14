<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Association $model */

$this->title = Yii::t('app', 'Update Association: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Associations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="association-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
