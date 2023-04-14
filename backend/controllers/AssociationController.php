<?php

namespace backend\controllers;

use common\models\Association;
use common\models\AssociationSearch;
use common\models\User;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
Use Yii;
use yii\filters\AccessControl;

/**
 * AssociationController implements the CRUD actions for Association model.
 */
class AssociationController extends Controller
{
    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            [
                'access' => [
                    'class' => AccessControl::class,
                    'rules' => [
                        [
                            'actions' => ['index', 'view', 'update', 'delete', 'create', 'desactivate', 'activate'],
                            'allow' => true,
                            'roles' => ['@'],
                        ],
                    ],
                ],

                'verbs' => [
                    'class' => VerbFilter::className(),
                    'actions' => [
                        'delete' => ['POST'],
                    ],
                ],
            ]
        );
    }

    /**
     * Lists all Association models.
     *
     * @return string
     */
    public function actionIndex()
    {
        $searchModel = new AssociationSearch();
        $dataProvider = $searchModel->search($this->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Association model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Association model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Association();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                $model->createAdminUserAndNotify();
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Association model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Association model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Association model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Association the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Association::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
    }


    /**
     * Activates an existing Association model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionActivate($id)
    {
        $model = $this->findModel($id);
        $statusBeforeActivationIsCreated = ($model->status == Association::ASSOCIATION_CREATED);

        $model->status = Association::ASSOCIATION_ACTIVATED;
        //get admin user account and activate it.
        $modelUser = User::findOne($model->admin_user_id);
        $modelUser->status = User::STATUS_ACTIVE;

        $modelUser->save();
        $model->save();

        if($statusBeforeActivationIsCreated){
            $model->sendCredentialsToAdminUser();
            //echo "notifications saved";
        }
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }


    /**
     * Desactivates an existing Association model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDesactivate($id)
    {
        $model = $this->findModel($id);

        $model->status = Association::ASSOCIATION_TEMPORARY_BLOCKED;
        //get admin user account and activate it.
        $modelUser = User::findOne($model->admin_user_id);
        $modelUser->status = User::STATUS_PHONE_NUMBER_NOT_VERIFIED;

        $modelUser->save();
        $model->save();
        
        return $this->render('view', [
            'model' => $model,
        ]);
    }
}
