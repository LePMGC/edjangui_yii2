<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Account $model */

$this->title = Yii::t('app', 'Update Account: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="account-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
