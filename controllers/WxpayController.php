<?php
namespace controllers;
use Endroid\QrCode\QrCode;
use Yansongda\Pay\Pay;
class WxpayController {

    protected $config = [
        'app_id' => 'wx426b3015555a46be', 
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',
        'notify_url' =>'http://fb52jx.natappfree.cc/wxpay/notify', //接收微信支付后台返回的通知
    ];

    public function pay(){
        $order = [
            'out_trade_no'=>time(),
            'total_fee'=>'1', 
            'body'=>'test body-测试',
        ];

        $pay = Pay::wechat($this->config)->scan($order);

        echo "<pre>";
        var_dump($pay->all());
    }
    //生成二维码

    public function qrcode()
    {
        $qrCode = new QrCode('weixin://wxpay/bizpayurl?pr=PtcH2K4');
        header('Content-Type: '.$qrCode->getContentType());
        echo $qrCode->writeString();
    }

    public function notify(){
        $pay = Pay::wechat($this->config);

        try{
            $data = $pay->verify();

            if($data->result_code=='SUCCESS'&&$data->return_code=='SUCCESS'){
                echo '共支付了：'.$data->total_fee.'分';
                echo '订单ID：'.$data->out_trade_no;
            }
        } catch (Exception $e) {
            var_dump( $e->getMessage() );
        }     
            $pay->success()->send();
    }
}

?>