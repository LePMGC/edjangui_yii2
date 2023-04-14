<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\Djangui $model */

$this->title = Yii::t('app', 'Create Djangui');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Djanguis'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="djangui-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
