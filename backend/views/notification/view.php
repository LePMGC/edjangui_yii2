<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model common\models\Notification */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Notifications'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="notification-view">

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <!-- <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?> -->
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'name',
            'description',
            'sms_content_en:ntext',
            'sms_content_fr:ntext',
            'email_content_en:ntext',
            'email_content_fr:ntext',
            [ 
                        'label' => $model->getAttributeLabel('send_sms'),
                        'value' => Yii::$app->formatter->asBoolean($model->send_sms),
                    ],
            [ 
                        'label' => $model->getAttributeLabel('send_email'),
                        'value' => Yii::$app->formatter->asBoolean($model->send_email),
                    ],
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
