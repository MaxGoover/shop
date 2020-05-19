<?php

namespace frontend\controllers\cabinet;

use shop\readModels\Shop\ProductReadRepository;
use shop\useCases\cabinet\WishlistService;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Controller;

class WishlistController extends Controller
{
    public $layout = 'cabinet';

    private $_service;
    private $_products;

    public function __construct(
        $id,
        $module,
        WishlistService $service,
        ProductReadRepository $products,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_service = $service;
        $this->_products = $products;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionAdd($id)
    {
        try {
            $this->_service->add(Yii::$app->user->id, $id);
            Yii::$app->session->setFlash('success', 'Success!');
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(Yii::$app->request->referrer ?: ['index']);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        try {
            $this->_service->remove(Yii::$app->user->id, $id);
        } catch (\DomainException $e) {
            Yii::$app->errorHandler->logException($e);
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
        return $this->redirect(['index']);
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $dataProvider = $this->_products->getWishList(\Yii::$app->user->id);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    ##################################################

    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'add' => ['POST'],
                    'delete' => ['POST'],
                ],
            ],
        ];
    }
}