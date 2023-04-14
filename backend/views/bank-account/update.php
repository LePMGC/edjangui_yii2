<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\BankAccount $model */

$this->title = Yii::t('app', 'Update Bank Account: {name}', [
    'name' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bank-account-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
