<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CashOut $model */

$this->title = Yii::t('app', 'Update Cash Out: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Outs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="cash-out-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
