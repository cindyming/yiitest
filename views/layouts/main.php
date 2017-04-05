<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use kartik\sidenav\SideNav;
use app\assets\AppAsset;

AppAsset::register($this);

$isAdmin = Yii::$app->user->identity->isAdmin();
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>

    <script>
        var _hmt = _hmt || [];
        (function() {
            var hm = document.createElement("script");
            hm.src = "//hm.baidu.com/hm.js?e9404ddcffdebefc426c52671ec0b323";
            var s = document.getElementsByTagName("script")[0];
            s.parentNode.insertBefore(hm, s);
        })();
    </script>
</head>
<body class="<?php echo $isAdmin ? 'u_admin' : 'u_member'; ?>">
<?php $this->beginBody() ?>

<div class="wrap <?php if (!$isAdmin): ?> member <?php endif ?>">
    <div class="top-header">
        <div class="container">
            <?php if ($isAdmin): ?>
                <?= $this->render('adminheader') ?>
            <?php else: ?>
                <?= $this->render('memberheader') ?>
            <?php endif ?>
        </div>
    </div>

    <div class="container">

        <?php if (Yii::$app->getSession()->get('big')): ?>
            <div class="Message big">
                <?= Yii::$app->getSession()->get('big');
                Yii::$app->getSession()->set('big',null);
                ?>
            </div>
        <?php endif ?>

        <?php if (Yii::$app->getSession()->get('message')): ?>
            <div class="Message">
                <?= Yii::$app->getSession()->get('message');
                Yii::$app->getSession()->set('message',null);
                ?>
            </div>
        <?php endif ?>

        <?php if (Yii::$app->getSession()->get('danger')): ?>
            <div class="fail" >
                <?= Yii::$app->getSession()->get('danger');
                Yii::$app->getSession()->set('danger',null);
                ?>
            </div>
        <?php endif ?>

        <?php if (!\app\models\System::loadConfig('maintenance')) :?>
            <div class="alert alert-danger">系统维护中，请不要操作任何数据，您的操作记录不会被保存</div>
        <?php endif ?>
        <!-- Breadcrumbs::widget([
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) -->
        <?= $content ?>
    </div>
</div>


<?php $this->endBody() ?>

<?php $this->beginBlock('js') ?>
$('body').find('[type=submit]').click(function(){
    $(this).hide();
});


function convertCurrency(currencyDigits) {

var MAXIMUM_NUMBER = 99999999999.99;  //最大值
// 定义转移字符
var CN_ZERO = "零";
var CN_ONE = "一";
var CN_TWO = "二";
var CN_THREE = "三";
var CN_FOUR = "四";
var CN_FIVE = "五";
var CN_SIX = "六";
var CN_SEVEN = "七";
var CN_EIGHT = "八";
var CN_NINE = "九";
var CN_TEN = "十";
var CN_HUNDRED = "佰";
var CN_THOUSAND = "仟";
var CN_TEN_THOUSAND = "万";
var CN_HUNDRED_MILLION = "亿";
var CN_DOLLAR = "元";
var CN_TEN_CENT = "角";
var CN_CENT = "分";
var CN_INTEGER = "整";

// 初始化验证:
var integral, decimal, outputCharacters, parts;
var digits, radices, bigRadices, decimals;
var zeroCount;
var i, p, d;
var quotient, modulus;

// 验证输入字符串是否合法
if (currencyDigits.toString() == "") {
alert("还没有输入数字");
return;
}
currencyDigits = currencyDigits.toString();
if (!currencyDigits.match(/\d/g)) {
alert("请输入有效数字");
return;
}

//判断是否输入有效的数字格式
var reg = /^((\d{1,3}(,\d{3})*(.((\d{3},)*\d{1,3}))?)|(\d+(.\d+)?))$/;
if (!reg.test(currencyDigits)) {
alert("请输入有效格式数字");
return;

}

currencyDigits = currencyDigits.replace(/,/g, "");
currencyDigits = currencyDigits.replace(/^0+/, "");
//判断输入的数字是否大于定义的数值
if (Number(currencyDigits) > MAXIMUM_NUMBER) {
alert("您输入的数字太大了");
return;
}

parts = currencyDigits.split(".");
if (parts.length > 1) {
integral = parts[0];
decimal = parts[1];
decimal = decimal.substr(0, 2);
}
else {
integral = parts[0];
decimal = "";
}
// 实例化字符大写人民币汉字对应的数字
digits = new Array(CN_ZERO, CN_ONE, CN_TWO, CN_THREE, CN_FOUR, CN_FIVE, CN_SIX, CN_SEVEN, CN_EIGHT, CN_NINE);
radices = new Array("", CN_TEN, CN_HUNDRED, CN_THOUSAND);
bigRadices = new Array("", CN_TEN_THOUSAND, CN_HUNDRED_MILLION);
decimals = new Array(CN_TEN_CENT, CN_CENT);

outputCharacters = "";
//大于零处理逻辑
if (Number(integral) > 0) {
zeroCount = 0;
for (i = 0; i < integral.length; i++) {
p = integral.length - i - 1;
d = integral.substr(i, 1);
quotient = p / 4;
modulus = p % 4;
if (d == "0") {
zeroCount++;
}
else {
if (zeroCount > 0) {
outputCharacters += digits[0];
}
zeroCount = 0;
outputCharacters += digits[Number(d)] + radices[modulus];
}
if (modulus == 0 && zeroCount < 4) {
outputCharacters += bigRadices[quotient];
}
}
outputCharacters += CN_DOLLAR;
}
// 包含小数部分处理逻辑
if (decimal != "") {
for (i = 0; i < decimal.length; i++) {
d = decimal.substr(i, 1);
if (d != "0") {
outputCharacters += digits[Number(d)] + decimals[i];
}
}
}
//确认并返回最终的输出字符串
if (outputCharacters == "") {
outputCharacters = CN_ZERO + CN_DOLLAR;
}
if (decimal == "") {
outputCharacters += CN_INTEGER;
}

//获取人民币大写
return outputCharacters;
}

$('input.amountNumber').on('input',function(){
    var number = $(this).val();
    if ($(this).hasClass('needBig')) {
    number = number * 10000;
    }
    number = convertCurrency(number);
    $(this).parent('div.form-group').find('.hint-block span.realAmount').text(number);
})

<?php $this->endBlock() ?>
<?php $this->registerJs($this->blocks['js'], \yii\web\View::POS_END); ?>
</body>
</html>
<?php $this->endPage() ?>
