<?php

namespace backend\controllers\blog;

use backend\forms\Blog\CommentSearch;
use shop\entities\Blog\Post\Post;
use shop\forms\manage\Blog\Post\CommentEditForm;
use shop\useCases\manage\Blog\CommentManageService;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class CommentController extends Controller
{
    private $_manageService;

    public function __construct(
        $id,
        $module,
        CommentManageService $manageService,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_manageService = $manageService;
    }

    /**
     * @param $post_id
     * @param integer $id
     * @return mixed
     */
    public function actionActivate($post_id, $id)
    {
        $post = $this->findModel($post_id);
        try {
            $this->_manageService->activate($post->id, $id);
        } catch (\DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['view', 'post_id' => $post_id, 'id' => $id]);
    }

    /**
     * @param $post_id
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($post_id, $id)
    {
        $post = $this->findModel($post_id);
        try {
            $this->_manageService->remove($post->id, $id);
        } catch (\DomainException $e) {
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CommentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * @param integer $post_id
     * @param integer $id
     * @return mixed
     */
    public function actionView($post_id, $id)
    {
        $post = $this->findModel($post_id);
        $comment = $post->getComment($id);

        return $this->render('view', [
            'post' => $post,
            'comment' => $comment,
        ]);
    }

    /**
     * @param integer $post_id
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($post_id, $id)
    {
        $post = $this->findModel($post_id);
        $comment = $post->getComment($id);

        $form = new CommentEditForm($comment);
        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $this->_manageService->edit($post->id, $comment->id, $form);
                return $this->redirect(['view', 'post_id' => $post->id, 'id' => $comment->id]);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }
        return $this->render('update', [
            'post' => $post,
            'model' => $form,
            'comment' => $comment,
        ]);
    }

    /**
     * @param integer $id
     * @return Post the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id): Post
    {
        if (($model = Post::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    ##################################################

    public function behaviors(): array
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
}
