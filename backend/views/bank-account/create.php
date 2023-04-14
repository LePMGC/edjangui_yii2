<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\BankAccount $model */

$this->title = Yii::t('app', 'Create Bank Account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bank-account-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
