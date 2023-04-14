<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LoanOptionTerm $model */

$this->title = Yii::t('app', 'Create Loan Option Term');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loan Option Terms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-option-term-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
