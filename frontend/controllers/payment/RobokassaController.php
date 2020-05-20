<?php

namespace frontend\controllers\payment;

use robokassa\Merchant;
use shop\entities\Shop\Order\Order;
use shop\readModels\Shop\OrderReadRepository;
use shop\useCases\Shop\OrderService;
use Yii;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use robokassa\ResultAction;
use robokassa\SuccessAction;
use robokassa\FailAction;

class RobokassaController extends Controller
{
    public $enableCsrfValidation = false;

    private $_orders;
    private $_service;

    public function __construct(
        $id,
        $module,
        OrderReadRepository $orders,
        OrderService $service,
        $config = [])
    {
        parent::__construct($id, $module, $config);
        $this->_orders = $orders;
        $this->_service = $service;
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'result' => [
                'class' => ResultAction::class,
                'callback' => [$this, 'resultCallback'],
            ],
            'success' => [
                'class' => SuccessAction::class,
                'callback' => [$this, 'successCallback'],
            ],
            'fail' => [
                'class' => FailAction::class,
                'callback' => [$this, 'failCallback'],
            ],
        ];
    }

    public function actionInvoice($id)
    {
        $order = $this->_loadModel($id);

        return $this->_getMerchant()->payment($order->cost, $order->id, 'Payment', null, null);
    }

    public function failCallback($merchant, $nInvId, $nOutSum, $shp)
    {
        $order = $this->_loadModel($nInvId);
        try {
            $this->_service->fail($order->id);
            return 'OK' . $nInvId;
        } catch (\DomainException $e) {
            return $e->getMessage();
        }
    }

    public function resultCallback($merchant, $nInvId, $nOutSum, $shp)
    {
        $order = $this->_loadModel($nInvId);
        try {
            $this->_service->pay($order->id);
            return 'OK' . $nInvId;
        } catch (\DomainException $e) {
            return $e->getMessage();
        }
    }

    public function successCallback($merchant, $nInvId, $nOutSum, $shp)
    {
        return $this->goBack();
    }

    private function _loadModel($id): Order
    {
        if (!$order = $this->_orders->findOwn(\Yii::$app->user->id, $id)) {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
        return $order;
    }

    private function _getMerchant(): Merchant
    {
         return Yii::$app->get('robokassa');
    }
}