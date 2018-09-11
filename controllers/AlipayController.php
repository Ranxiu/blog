<?php
namespace controllers;

use Yansongda\Pay\Pay;

class AlipayController
{   

    //tdqpka3374@sandbox.com
    public $config = [
        'app_id' => '2016091600527352',
        // 通知地址
        // 'sign_type' => 'RSA2',
        // 'charset' =>'UTF-8',
        'notify_url' => 'http://b5f4402e.ngrok.io/alipay/notify', // 支付成功后台通知地址
        // 'notify_url' => 'http://requestbin.fullcontact.com/1hmzw3j1', // 支付成功后台通知地址
        // 跳回地址
        'return_url' => 'http://localhost:8000/alipay/return',

        // 'log' => [ // optional
        //     'file' => './logs/alipay.log',
        //     'level' => 'debug',
        //     'type' => 'single', // optional, 可选 daily.
        //     'max_file' => 30, // optional, 当 type 为 daily 时有效，默认 30 天
        // ],
        // 支付宝公钥
        'ali_public_key' => 'MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAtYvPNIQetrwPMHbvxaYvVN9pAdWBy4MyUqeikAwc7bij0u3AuNjPdoAI8M10Y4lrXldY98Uihgx7BIpvC9QyQo/mRT5t5v9s0zbHEgPhwEVFxRO1nNNRF4yjP6JHIA8Nz/STKdV14ulkDIR4cVLRvXpymGdIcJTlOezOopPEmWHkl4mAAIXjcZu0H/r/J6M+5bPCYhEeJrJt4ZraqaAI7DSZiekBm4pGPJjvnlFbX/7FI9OnxTg1jYo7kXQyiLP+Pe7hlQ15D61/R14RLnOixZv1D636FGyHDEVS7XZdFbDozAUbR2IwjqdobPXWfUE0DULgkYDqqzJS0OIxf4IEkwIDAQAB',
        // 商户应用密钥
        'private_key' => 'MIIEpQIBAAKCAQEA00ND6VE7dbBOao8teewC66WBZfpMO+q7vgI+bpm6pIcS290sHulp27ecWX2Ujp5jCj0OF15cTGYO2QUyV8kBPSuwuMA5PbfbVnT2LTU5QXHtjIGi9UlSvLv6xTIdeEqMku9RpSX6mJgtyf5SpUD0LpFLgI7nU54sokOuxUdutWR46ijmQ2igHiw7gDCapK5mbcAvHZ9LBSMt7ZYAeVI0/0ChhW9j6zjkS9shH6kntKSNTxSYERKAlvXux1bOGCyFZy91QtZoQuQVoqz8yRCIrDs3Nm8jDHFy98Pp3CmZwDm0+EHv1LFIeFSCQrT+ADWZ/3ALEI80NvhEtuGR/2JLTQIDAQABAoIBAQCm4/B2FFHgetK3ozvNoP/9n9VE9iHbA2gkhilDvfWPm5TuUx5TnRifFcFRgL8mm0CqCelj1IsdX1JDZrOKXaO46xbEHDYb525CMkt3EyCT7qg14wMUukO+DNHhjeFx8ZJzUNMyn3oDqdQiSeKH4XQEhYsbl8huafnghY8/EVsHt1O82UCKYZnnQMG8rPS+RNiXbVsv6g+WjyB8MPUM2ZWQPyGhIfnIOgIvcXN9SRsujGIPwVLhoO2ctky5TJ5YcUVcu7P+qPW6KOzSer433y1RwvI2ds7vNOtecy2k+ANFxWKVHDzy83xfuxpfGqThIPViyLLOfEsH6tD5MpKkN7kBAoGBAO01yV5PVS2kraSsZGuUHy9TiRL7bmYhVLxHE5uLGwmpWf8sUi/BCEmQo2XKD5RH9wwQAJ8ljUtjVCR5IT8NNj/uiL4EntBwKMZUScllgzPkO37qq3hanMU2txipHtMgFzBWTUPrD06aHiQ+dU0cKSPKcJi1yTUHKlwct0uzSSz9AoGBAOP/T58yEphjAynCOHJ+x7W05b7tsPc94vCTaltKCVlfQMIIZdZ17MAbRDe2Dy3/1f1Rz9IPM+FUmqSWwIcqX3ylFuYbj0B2qr1wVJmxYCt1TSy6ZzZxfYUDLbWRPc35eTU1HhkyHudb1OLDNddSlagNLB6iTIGq/7AiDKBqoBCRAoGBALxK87tPEfgXPlb3GQdkHpT8pFRTDUE4uAB0ExZnB2FV8sBhOCP8AE4U5/9Gs7MqpOoIUdYCgvQStpn1JPTd0fBRsm3TURV30oYMJj4dvFYWlzuZn977XnIViqWiqXtBf+a4usBs+EuWftKaWZaKAgYNDe/KbRsQwwk3KjCG8lEpAoGAftvDALAKurF0xBpl8AbstgTWRHramWbcy8EqT2sNcqXWUV/80exBc854EYuCPLT56v5HZPjChCDf/q5Gp66C/MaazCQPe+3LKKbE29Ne0C9vZnC9FwTks0rGdx0+R0oFD/7jJV/G5DIrbJTzc0BnNt9FajIh4ZA0vlrjrapGL5ECgYEAq6M53Aixq+ulUcuhszSpK4K3fzy/HKCMbjLevMDuAPCO+kCvdr6eiL4LeR1ecwcCf42slSX3JfutJB+CYy+otddyBCKPfSY305wtSFyJ/fiS7tAEEIgLJu6yhi666OghIUzGCeRu6Og/iEa2gd6FLABn72/pA7qVqNMDrFk47V8=',
        // 沙箱模式（可选）
        'mode' => 'dev',
    ];
    // 发起支付
    public function pay()
    {   
        //接收订单编号
        $sn = $_POST['sn'];
        //取出订单信息
        $order = new \models\Order;
        //根据订单编号取出订单信息
        $data = $order->findBySn($sn);
        
        //如果订单还未支付就跳支付宝
        if($data['status']==0){
            //跳转到支付宝
            $alipay = Pay::alipay($this->config)->web([
                'out_trade_no' => $sn,
                'total_amount'=> $data['money'],
                'subject' => '智聊系统用户充值——————'.$data['money'].'元',
            ]);
            $alipay->send();
        }else{
            die('订单状态不允许支付~');
        }
        
    }
    // 支付完成跳回
    public function return()
    {
        $data = Pay::alipay($this->config)->verify(); // 是的，验签就这么简单！

        // $data->charset = 'UTF-8';
        // $data->sign = 'yViKms0nk0R07iZEC5Ukul7nxRSYoCY1XFYwn7w7N/Op/fY7PYUetzl7QTK3h/GQQkeEt+AX4cfW0K2Xbi4doMRlpw8QdKWwuOWw7XsddZUYwseXXvpJr3j7ddkbmnaHgWP0oueGPAaGbmCG08gRjgIXgyB2WIFMu4Goz4BCRTsH3Hh4iB9VhJUlPE+efP0ZQnRMsxtvfwCAbVy+0B1lMNbtCnN7AkoWTXof9WhVxTwHH5PzpCDdsE/ofvEHE6ruCjBXIwxxQr3igKQE8+Nk+C6fqhAmRKB/b9Rlf7zfzXx63b2FcrErqjSAjXncTQvdsCyuoIXpwzM7JxGYWTPTrw==';

        echo '<h1>支付成功！</h1> <hr>';
        echo '<pre>';
       var_dump (json_encode($data->all()));
        // echo '订单ID：'.$data->out_trade_no ."\r\n";
        // echo '支付总金额：'.$data->total_amount ."\r\n";
        // echo '支付状态：'.$data->trade_status ."\r\n";
        // echo '商户ID：'.$data->seller_id ."\r\n";
        // echo 'app_id：'.$data->app_id ."\r\n";
    }
    // 接收支付完成的通知
    public function notify()
    {
        $alipay = Pay::alipay($this->config);
        try{
            $data = $alipay->verify(); // 验签
            // 这里需要对 trade_status 进行判断及其它逻辑进行判断，在支付宝的业务通知中，只有交易通知状态为 TRADE_SUCCESS 或 TRADE_FINISHED 时，支付宝才会认定为买家付款成功。
            if($data->trade_status == 'TRADE_SUCCESS' || $data->trade_status == 'TRADE_FINISHED')
            {
                // 更新订单状态
                $order = new \models\Order;
                // 获取订单信息
                $orderInfo = $order->findBySn($data->out_trade_no);
                // var_dump($orderInfo);die;
                // 如果订单的状态为未支付状态 ，说明是第一次收到消息，更新订单状态 
                if($orderInfo['status'] == 0)
                {   
                    //开启事物
                    $order->startTrans();
                    // 设置订单为已支付状态
                    $ret1 = $order->setPaid($data->out_trade_no);
                    // 更新用户余额
                    $user = new \models\User;
                    $ret2 = $user->addMoney($orderInfo['money'], $orderInfo['user_id']);
                 
                    if($ret1 && $ret2){
                        //提交事物
                        $order->commit();
                    }else{
                        //回事事物
                        $order->rollback();
                    }
                }
            }
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
        // 返回响应
        $alipay->success()->send();
    }
    //退款
    public function refund(){
        //生成唯一退款订单号
        $refundNo = md5(rand(1,99999).microtime());

        try {
            //退款
            $ret = Pay::alipay($this->config)->refund([
                'out_trade_no'=>'1536321725', //之前成功的订单流水号
                'refund_amount'=>5000,    //退款金额
                'out_request_no' => $refundNo,  //退款订单号
            ]);

            if($ret->code==10000){
                echo '退款成功！';
            }else{
                echo '退款失败，错误信息'.$ret->sub_msg;
                echo '错误信息:'.$ret->sub_code;
            }
        }
        catch(\Exception $e){
            var_dump($e->getMessage());
        }

      
    }
}