<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Loan $model */

$this->title = Yii::t('app', 'Create Loan');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Loans'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="loan-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
