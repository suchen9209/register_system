layui.use(['form', 'layer', 'laydate', 'table', 'laytpl', 'excel', 'jquery'], function() {
    var form = layui.form;
    var laypage = layui.laypage;
    layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;
        excel = layui.excel;
        state = ''; //状态
        tid = '';
    var show_data = new Array();
    var show_field = new Array();
    var show_obj = {};
    // 设置本地储存
    $.ajax({
        url: "/tool/init",
        type: "GET",
        async: false,
        dataType: 'json',
        success(res) {
            layui.data('weight', {
                key: 'weight',
                value: res.user_info.weight
            });
            tid = res.user_info.tid;
        },
        error() {
            layer.alert('登录信息已过期，请重新登录', function() {
                   window.location.href = '/SignUpCMS/page/login/login.html';
            });
        }
    });

    //获取url中的参数
    function getUrlParam(name) {
        var reg = new RegExp("(^|&)" + name + "=([^&]*)(&|$)"); //构造一个含有目标参数的正则表达式对象
        var r = window.location.search.substr(1).match(reg); //匹配目标参数
        if (r != null) return unescape(r[2]);
        return null; //返回参数值
    }
    state = getUrlParam('state');
    $.ajax({
        url: "/tool/init/list_header_json",
        type: "GET",
        async: false,
        dataType: 'json',
        success(res) {

            for (var i = 0; i < res.length; i++) {
                show_data.push(res[i]);
                if (res[i].type == "image") {
                    res[i].title = res[i].title;
                    res[i].align = 'center';
                    res[i].templet = "<div><a href='{{ d." + res[i].field + "}}' target='_blank'><img height='26' src='{{ d." + res[i].field + "}}'></a></div>";
                    //var json = { "title": res[i].title, "align": "center", "templet": "<div><a href='{{ d.extra_filed1}}' target='_blank'><img height='26' src='{{ d.extra_filed1}}'></a></div>" };
                    //res[i] = json;
                } else {
                    res[i].align = 'center';
                }
            }

            var localTest = layui.data('weight');
            // var json1 = { "title": '操作', "templet": "#newsListBar", "fixed": "right", "align": "center" };
            var json0 = { "checkbox": true, "fixed": true }; 
            if (localTest.weight >= 50 && state == 0) {
                res.unshift(json0);
                $(".operate").show();
                // res.push(json1);
            }else{
                $(".operate").hide();
            }
            var tableIns = table.render({
                elem: '#newsList',
                url: '/tool/applicant?tid=' + tid + '&state=' + state,
                limit: 15,
                limits: [15, 30, 45, 60],
                page: true,
                //,…… //其他参数
                cols: [res],
                done: function(res, curr, count) {
                    console.log(res);
                    $(".layui-table-box").find("[data-field='state']").css("display", "none");
                    $(".layui-table-box").find("[data-field='id']").css("display", "none");
                }
            });
        },
        error() {
            layer.alert('登录信息已过期，请重新登录', function() {
                   window.location.href = '/SignUpCMS/page/login/login.html';
            });
        }
    });

    // 搜索
    $('.search_btn').on('click', function() {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : '';
    });

    // 验证手机号
    function isPhoneNo(phone) {
        var pattern = /^1[34578]\d{9}$/;
        return pattern.test(phone);
    }
    // 导出数据
    function exportApiDemo() {
        $.ajax({
            url: "/tool/applicant?tid="+tid+'&state='+state+'&limit=5000',
            type: "GET",
            dataType: 'json',
            success(res) {
                for(var i in show_data){
                    show_field.push(show_data[i].field);
                    show_obj[show_data[i].field] = show_data[i].title;
                }
                var data = res.data;

                // 重点！！！如果后端给的数据顺序和映射关系不对，请执行梳理函数后导出
                data = excel.filterExportData(data, show_field);
                // 重点2！！！一般都需要加一个表头，表头的键名顺序需要与最终导出的数据一致
                data.unshift(show_obj);

                var timestart = Date.now();
                excel.exportExcel(data, '导出接口数据.xlsx', 'xlsx');
            },
            error() {
                layer.alert('登录信息已过期，请重新登录', function() {
                   window.location.href = '/SignUpCMS/page/login/login.html';
                });
            }
        });
    }

    $('.export_data').on('click', function() {
        exportApiDemo();
    });
    //列表操作
    // table.on('tool(newsList)', function(obj) {
    //     var layEvent = obj.event,
    //         data = obj.data;

    //     var userid = obj.data.id;
    //     var username = obj.data.username;
    //     var name = data.name;
    //     var phone = data.phone;
    //     var idcard = data.idcard;
    //     console.log(userid);
    //     if (layEvent === 'adopt') { //上机
    //         $.ajax({
    //             url: "/tool/applicant/update/" + userid,
    //             type: "POST",
    //             data: {
    //                 state: 10
    //             },
    //             dataType: 'json',
    //             success: function(data) {
    //                 if (data.code == 200) {
    //                     layer.msg(data.message, { time: 1000 }, function() {
    //                         //回调
    //                         window.location.reload();
    //                     })
    //                 } else {
    //                     layer.msg(data.message);
    //                 }

    //             },
    //             error: function(err) {
    //                 console.log(err);
    //             }
    //         });
    //         // 未通过
    //     } else if (layEvent === 'no-pass') {
    //         $.ajax({
    //             url: "/tool/applicant/update/" + userid,
    //             type: "POST",
    //             data: {
    //                 state: 2
    //             },
    //             dataType: 'json',
    //             success: function(data) {
    //                 if (data.code == 200) {
    //                     layer.msg(data.message, { time: 1000 }, function() {
    //                         //回调
    //                         window.location.reload();
    //                     })
    //                 } else {
    //                     layer.msg(data.message);
    //                 }

    //             },
    //             error: function(err) {
    //                 console.log(err);
    //             }
    //         });
    //         // 替补
    //     } else if (layEvent === 'Substitute') {
    //         $.ajax({
    //             url: "/tool/applicant/update/" + userid,
    //             type: "POST",
    //             data: {
    //                 state: 5
    //             },
    //             dataType: 'json',
    //             success: function(data) {
    //                 if (data.code == 200) {
    //                     layer.msg(data.message, { time: 1000 }, function() {
    //                         //回调
    //                         window.location.reload();
    //                     })
    //                 } else {
    //                     layer.msg(data.message);
    //                 }

    //             },
    //             error: function(err) {
    //                 console.log(err);
    //             }
    //         });
    //     }
    // });

     // 搜索and批量操作
    var $ = layui.$,
        active = {
            reload: function() {
                var name = $("#searchVal").val();
                table.reload('newsList', {
                    // url: '//tool/applicant?tid='+tid+'&state=-1',
                    where: {
                        name: name
                    }
                });
            },
            getCheckData: function() { //获取选中数据
                var checkStatus = table.checkStatus('newsList'),
                    data = checkStatus.data;
                if (data.length == 0) {
                    layer.msg('请选中之后再操作！！！', { icon: 5 });
                } else {
                    var str = "";
                    for (var i = 0; i < data.length; i++) {
                        str += data[i].id + ",";
                    }
                    if (str.length > 0) {
                        str = str.substr(0, str.length - 1);
                    }
                    layer.open({
                        type: 2,
                        title: '',
                        shadeClose: true,
                        shade: 0.8,
                        area: ['600px', '400px'],
                        content: 'page/operate/index.html', //iframe的url
                        success: function(layero, index) {
                            var body = layer.getChildFrame('body', index);
                            var iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
                            body.find('.cz').attr('aids', str);
                        },
                        // end: function() {
                        //     var searchVal = $(".searchVal").val();
                        //     if (searchVal != '') {
                        //         if (isPhoneNo(searchVal)) {
                        //             username = '';
                        //             phone = searchVal;
                        //             name = '';
                        //             idcard = '';
                        //         } else {
                        //             username = searchVal;
                        //             phone = '';
                        //             name = '';
                        //             idcard = '';
                        //         }
                        //         tableIns.reload({
                        //             url: 'https://pay.imbatv.cn/api/user/get_user_list',
                        //             where: {
                        //                 username: username,
                        //                 phone: phone,
                        //                 name: name,
                        //                 idcard: idcard
                        //             },
                        //             page: {
                        //                 curr: 1 //重新从第 1 页开始
                        //             }
                        //         });
                        //     } else {
                        //         tableIns.reload({});
                        //     }
                        // }
                    });
                }
            }
        };

    $('.operate').on('click', function() {
        var type = $(this).data('type');
        active[type] ? active[type].call(this) : ''; 
    });
})