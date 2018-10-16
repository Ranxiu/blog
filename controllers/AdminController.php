<?php
namespace controllers;

class AdminController {

    //网站后台1
    public function admin(){
        view('home.admin');
    }
    //后台头部
    public function top(){
        view('home.top');
    }
    //后台左侧
    public function menu(){
        view('home.menu');
    }
    //后台右侧
    public function main(){
        view('home.main');
    }
     //后台右侧2
     public function main2(){
        view('home.main');
    }
    //应用
    //电子邮件
    public function email(){view('apps.email');}
    //日历
    public function calendar(){view('apps.calendar');}
    //聊天应用
    public function chat_app(){view('apps.chat-app');}
    //练习
    public function contact(){view('apps.contact');}
    
    //ui元素
    //警告
    public function alert(){view('elements.alert');}
    //徽章
    public function badge(){view('elements.badge');}
    //按钮
    public function buttons(){view('elements.buttons');}
    //牌
    public function cards(){view('elements.cards');}
    //清单
    public function lists(){view('elements.lists');}
    //活版印刷
    public function typography(){view('elements.typography');}

    //组件
    //手风琴
    public function accordion(){view('compones.accordion');}
    //carousel
    public function carousel(){view('compones.carousel');}
    //dropdown
    public function dropdown(){view('compones.dropdown');}
    //modals
    public function modals(){view('compones.modals');}
    //通知
    public function notifications(){view('compones.notifications');}
    //popover
    public function popover(){view('compones.popover');}
    //进度条
    public function slider_progress(){view('compones.slider-progress');}
    //标签
    public function tabs(){view('compones.tabs');}
    //提示
    public function tooltips(){view('compones.tooltips');}

    //icons
    //line icons 
    public function line_icons(){view('icons.line-icons');}
    //carousel
    public function fontawesome_icons(){view('icons.fontawesome-icons');}
    //dropdown
    public function material_icons(){view('icons.material-icons');}

    //表单
    //表单元素
    public function form_elements(){view('froms.form-elements');}
    //表单布局
    public function form_layouts(){view('froms.form-layouts');}
    //表单验证
    public function form_validation(){view('froms.form-validation');}
 
    //表格
    //基本表格
    public function basic_table(){view('tables.basic-table');}
    //数据表格
    public function data_table(){view('tables.data-table');}

    //图表
    //marris图表
    public function charts_morris(){view('charts.charts-morris');}
    //ChartJs
    public function chartjs(){view('charts.chartjs');}
    //Flot图表
    public function charts_flot(){view('charts.charts-flot');}

    //地图
    //谷歌地图
    public function google_map(){view('maps.google-map');}
    //Flot图表
    public function vector_map(){view('maps.vector-map');}

    //网页
    //profile
    public function profile(){view('pages.profile');}
    //发票
    public function invoice(){view('pages.invoice');}
    //常见问题
    public function faq(){view('pages.faq');}
    //登录
    public function login(){view('pages.login');}
    //注册
    public function sign_up(){view('pages.sign-up');}
    //404
    public function bad(){view('pages.404');}

}




?>