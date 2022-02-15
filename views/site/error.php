<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $name;
$this->params['breadcrumbs'] = [['label' => $this->title]];
?>
<div class="error-page">
    <div class="error-content" style="margin-left: auto;">
        <h3><i class="fas fa-exclamation-triangle text-danger"></i> <?= Html::encode($name) ?></h3>

        <p>
            <?= nl2br(Html::encode($message)) ?>
        </p>

        <p>
			Вышеуказанная ошибка произошла, когда веб-сервер обрабатывал ваш запрос.
			Пожалуйста, свяжитесь с нами, если вы считаете, что это ошибка сервера. Спасибо.
            Тем временем, вы можете <?= Html::a('return to dashboard', Yii::$app->homeUrl); ?>
            или попробуйте воспользоваться формой поиска.
        </p>

        <form class="search-form" style="margin-right: 190px;">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Search">

                <div class="input-group-append">
                    <button type="submit" name="submit" class="btn btn-danger"><i class="fas fa-search"></i>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

