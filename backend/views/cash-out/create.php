<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CashOut $model */

$this->title = Yii::t('app', 'Create Cash Out');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Outs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-out-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
