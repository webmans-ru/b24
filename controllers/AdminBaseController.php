<?php

namespace app\modules\b24\controllers;

use Yii;
use \yii\web\HttpException;

class AdminBaseController extends AdminController {

    public function beforeAction($action) {
        $this->enableCsrfValidation = false;        
        if (in_array($action->id, ['add-portal-auth'])) {
            return parent::beforeAction($action);
        }
        
        $session = Yii::$app->session;
        if($session['AccessParams']){
            return parent::beforeAction($action);
        }
        $request = Yii::$app->request;
        //Yii::warning($request);

        if (null === $request->get('DOMAIN') or null === $request->post('member_id') or null === $request->post('AUTH_ID') or null === $request->post('REFRESH_ID')) {
            throw new HttpException(404, 'Приложение необходимо запустить из портала Битрикс24');
        }
        $component24 = new \app\components\b24Tools();
        $arAccessParams = $component24->prepareFromRequest(Yii::$app->request->post(), Yii::$app->request->get());
        $errors = $component24->checkB24Auth();
        if ($errors) {
            throw new HttpException(403, 'В доступе отказано');
        }
        
        $session->set('accessAllowed', true);
        $session['AccessParams'] = $arAccessParams;
        return parent::beforeAction($action);
    }

    public function actionInstall() {
            return $this->render('install');        
    }

    public function actionIndex() {
        return $this->render('index');
    }


}
