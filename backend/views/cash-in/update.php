<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CashIn $model */

$this->title = Yii::t('app', 'Update Cash In: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Ins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="cash-in-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
