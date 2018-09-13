<?php
namespace controllers;
use Endroid\QrCode\QrCode;
use Yansongda\Pay\Pay;
class WxpayController {

    protected $config = [
        'app_id' => 'wx426b3015555a46be', 
        'mch_id' => '1900009851',
        'key' => '8934e7d15453e97507ef794cf7b0519d',
        'notify_url' =>'http://yu5yaf.natappfree.cc/wxpay/notify', //接收微信支付后台返回的通知
    ];

    public function pay(){
        
        //接收订单编号
        $sn = $_POST['sn'];
        //取出订单信息
        $order = new \models\Order;
        //根据订单取出订单信息
        $data = $order->findBySn($sn);

        if($data['status']==0){

            $pay = Pay::wechat($this->config)->scan([
                'out_trade_no'=>$data['sn'],
                'total_fee'=>$data['money']*100, //单位：分
                'body'=>'智聊系统用户充值：'.$data['money'].'元',
            ]);

            if($pay->return_code=='SUCCESS'&& $pay->result_code=='SUCCESS'){
                //加载二维码视图
                view('users.wxpay',[
                    'code'=>$pay->code_url,
                    'sn'=>$sn,
                ]);
            }
        }else{
            die('订单状态不允许支付~');
        }
    }
    //生成二维码

    public function qrcode()
    {   
        $code = $_GET['code'];

        $qrCode = new QrCode($code);
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