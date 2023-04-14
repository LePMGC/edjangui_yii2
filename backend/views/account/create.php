<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Account $model */

$this->title = Yii::t('app', 'Create Account');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="account-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
