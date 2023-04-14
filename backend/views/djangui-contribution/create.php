<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var common\models\DjanguiContribution $model */

$this->title = Yii::t('app', 'Create Djangui Contribution');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Djangui Contributions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="djangui-contribution-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
