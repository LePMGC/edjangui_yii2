<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\CashIn $model */

$this->title = Yii::t('app', 'Create Cash In');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Cash Ins'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cash-in-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
