<?php

namespace frontend\controllers\shop;

use shop\cart\Cart;
use shop\forms\Shop\Order\OrderForm;
use shop\useCases\Shop\OrderService;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class CheckoutController extends Controller
{
    public $layout = 'blank';

    private $_service;
    private $_cart;

    public function __construct($id, $module, OrderService $service, Cart $cart, $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_service = $service;
        $this->_cart = $cart;
    }

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $form = new OrderForm($this->_cart->getWeight());

        if ($form->load(Yii::$app->request->post()) && $form->validate()) {
            try {
                $order = $this->_service->checkout(Yii::$app->user->id, $form);
                return $this->redirect(['/cabinet/order/view', 'id' => $order->id]);
            } catch (\DomainException $e) {
                Yii::$app->errorHandler->logException($e);
                Yii::$app->session->setFlash('error', $e->getMessage());
            }
        }

        return $this->render('index', [
            'cart' => $this->_cart,
            'model' => $form,
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
        ];
    }
}