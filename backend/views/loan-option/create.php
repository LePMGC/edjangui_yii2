<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LoanOption $model */

$this->title = Yii::t('app', 'Create Loan Option');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loan Options'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-option-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
