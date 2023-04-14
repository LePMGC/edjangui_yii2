<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;
use common\models\Association;

/** @var yii\web\View $this */
/** @var common\models\BankAccount $model */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bank Accounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="bank-account-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            [ 
                'label' => $model->getAttributeLabel('loan_allowed'),
                'value' => $model->loan_allowed ? Yii::t('app', 'YES') : Yii::t('app', 'NO'),
            ],
            [ 
                'label' => $model->getAttributeLabel('cash_in_allowed'),
                'value' => $model->cash_in_allowed ? Yii::t('app', 'YES') : Yii::t('app', 'NO'),
            ],
            [ 
                'label' => $model->getAttributeLabel('cash_out_allowed'),
                'value' => $model->cash_out_allowed ? Yii::t('app', 'YES') : Yii::t('app', 'NO'),
            ],
            'fix_cash_in_amount',
            'min_balance_for_loan',
            'min_cash_in_amount',
            [ 
                                        'label' => $model->getAttributeLabel('created_by'),
                                        'value' => User::findOne($model->created_by)->name,
                                    ],
                                    [ 
                                        'label' => $model->getAttributeLabel('created_on'),
                                        'value' => Yii::$app->formatter->asDateTime($model->created_on),
                                    ],
                                    [ 
                                        'label' => $model->getAttributeLabel('updated_by'),
                                        'value' => User::findOne($model->updated_by)->name,
                                    ],
                                    [ 
                                        'label' => $model->getAttributeLabel('updated_on'),
                                        'value' => Yii::$app->formatter->asDateTime($model->updated_on),
                                    ]
        ],
    ]) ?>

</div>
