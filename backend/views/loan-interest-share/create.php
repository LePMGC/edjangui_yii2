<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\LoanInterestShare $model */

$this->title = Yii::t('app', 'Create Loan Interest Share');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loan Interest Shares'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-interest-share-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
