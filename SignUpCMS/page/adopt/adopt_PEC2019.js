layui.use(['form', 'util', 'layer', 'laydate', 'table', 'laytpl', 'util','excel','jquery'], function() {
    var form = layui.form;
    var util = layui.util;
    layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;
        excel = layui.excel;

        var tableIns = table.render({
        elem: '#newsList',
        url: 'http://apply.imbatv.cn//tool/applicant?tid=4&state=10',
        limit: 15,
        limits: [15, 30, 45, 60],
        page: true,
        //,…… //其他参数
         cols: [
            [
                { field: 'id', title: 'ID', align: 'center', width: 50 },
                { field: 'name', title: '姓名', width: 150, align: "center" },
                { field: 'phone', title: '手机', align: 'center' },
                { field: 'qq', title: 'QQ', align: 'center' },
                { field: 'extra_filed1', title: '游戏类型', align: 'center' },
                { field: 'extra_filed2', title: '游戏赛区', align: 'center' },
                { field: 'extra_filed3', title: '战队名称', align: 'center' }
                
            ]
        ],
        done: function(res, curr, count) {
            console.log(res);
            $(".layui-table-box").find("[data-field='state']").css("display", "none");
            $(".layui-table-box").find("[data-field='id']").css("display", "none");
        }
    });

    var $ = layui.$, active = {
        reload: function(){
            var searchVal = $(".searchVal").val();
            if (isPhoneNo(searchVal)) {
                username = '';
                phone = searchVal;
                name = '';
                idcard = '';
            } else {
                username = searchVal;
                phone = '';
                name = '';
                idcard = '';
            }
            table.reload('newsList', {
                where: {
                    username: username,
                    phone: phone,
                    name: name,
                    idcard: idcard
                } ,page: {
                    curr: 1 //重新从第 1 页开始
                }
            });
        }
    };

    $('.search_btn').on('click', function(){
        exportApiDemo();
    });
    // 导出数据
    function exportApiDemo() {
        $.ajax({
            url: "http://apply.imbatv.cn//tool/applicant?tid=4&state=10&limit=2000",
            type: "GET",
            dataType: 'json',
            success(res) {
                console.log(res);
                var data = res.data;
                data = excel.filterExportData(data, [
                    'name','phone', 'qq', 'extra_filed1','extra_filed2','extra_filed3'
                ]);
                // 重点2！！！一般都需要加一个表头，表头的键名顺序需要与最终导出的数据一致
                data.unshift({  name: "姓名", phone: '手机',  qq: 'qq', extra_filed1: '游戏类型',extra_filed2: '游戏赛区',extra_filed3: '战队名称' });

                var timestart = Date.now();
                excel.exportExcel(data, '导出接口数据.xlsx', 'xlsx');
            },
            error() {
                layer.alert('获取数据失败，请检查是否部署在本地服务器环境下');
            }
        });
    }
    // 验证手机号
    function isPhoneNo(phone) {
        var pattern = /^1[34578]\d{9}$/;
        return pattern.test(phone);
    }

    //充值查询
    function Recharge(edit) {
        var uid = edit.uid;
        var index = layui.layer.open({
            title: "充值记录查询",
            type: 2,
            content: "recharge.html?uid=" + uid,
            success: function(layero, index) {
                var body = layui.layer.getChildFrame('body', index);
                if (edit) {
                    body.find(".newsName").val(edit.newsName);
                    body.find(".abstract").val(edit.abstract);
                    body.find(".thumbImg").attr("src", edit.newsImg);
                    body.find("#news_content").val(edit.content);
                    body.find(".newsStatus select").val(edit.newsStatus);
                    body.find(".openness input[name='openness'][title='" + edit.newsLook + "']").prop("checked", "checked");
                    body.find(".newsTop input[name='newsTop']").prop("checked", edit.newsTop);
                    form.render();
                }
                setTimeout(function() {
                    layui.layer.tips('点击此处返回在线会员列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                }, 300)
            }
        })
        layui.layer.full(index);
    }
    //消费查询
    function consumption(edit) {
        var uid = edit.uid;
        var index = layui.layer.open({
            title: "消费记录查询",
            type: 2,
            content: "consumption.html?uid=" + uid,
            success: function(layero, index) {
                var body = layui.layer.getChildFrame('body', index);
                if (edit) {
                    body.find(".newsName").val(edit.newsName);
                    body.find(".abstract").val(edit.abstract);
                    body.find(".thumbImg").attr("src", edit.newsImg);
                    body.find("#news_content").val(edit.content);
                    body.find(".newsStatus select").val(edit.newsStatus);
                    body.find(".openness input[name='openness'][title='" + edit.newsLook + "']").prop("checked", "checked");
                    body.find(".newsTop input[name='newsTop']").prop("checked", edit.newsTop);
                    form.render();
                }
                setTimeout(function() {
                    layui.layer.tips('点击此处返回在线会员列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                }, 500)
            }
        })
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
    }



    //列表操作
    table.on('tool(newsList)', function(obj) {
        console.log(obj);
        var layEvent = obj.event,
            data = obj.data;
        var userid = obj.data.uid;
        if (layEvent === 'recharge') { //充值记录
            Recharge(data);
        } else if (layEvent === 'consumption') { //消费记录
            consumption(data);
        } else if (layEvent === 'down-cp') { //下机
            // alert(1);
            // var user_id = obj.data.uid;
            // alert(user_id);
            // $.ajax({
            //     url: "https://pay.imbatv.cn/api/machine/down",
            //     type: "post",
            //     data: {
            //         user_id: user_id,
            //         op: 'down',
            //     },
            //     dataType: 'json',
            //     success: function(data) {
            //         location.reload();
            //     },
            //     error: function(err) {}
            // });

            layer.open({
                type: 2,
                title: '',
                shadeClose: true,
                shade: 0.8,
                area: ['800px', '500px'],
                content: 'page/alert_6/give.html?userid=' + userid, //iframe的url
                success: function(layero, index) {
                    var body = layer.getChildFrame('body', index);
                    var iframeWin = window[layero.find('iframe')[0]['name']]; //得到iframe页的窗口对象，执行iframe页的方法：iframeWin.method();
                    body.find('.cz').attr('userid', userid);
                },
                end: function() {
                    var searchVal = $(".searchVal").val();
                    if (searchVal != '') {
                        if (isPhoneNo(searchVal)) {
                            username = '';
                            phone = searchVal;
                            name = '';
                            idcard = '';
                        } else {
                            username = searchVal;
                            phone = '';
                            name = '';
                            idcard = '';
                        }
                        tableIns.reload({
                            url: 'https://pay.imbatv.cn/api/user/get_user_list',
                            where: {
                                username: username,
                                phone: phone,
                                name: name,
                                idcard: idcard
                            },
                            page: {
                                curr: 1 //重新从第 1 页开始
                            }
                        });
                    } else {
                        tableIns.reload({});
                    }
                }
            });
        } else if (layEvent === 'information') { //绑定信息
            layer.open({
                type: 2,
                title: '',
                shadeClose: true,
                shade: 0.8,
                area: ['600px', '300px'],
                content: 'page/alert_3/recharge.html?userid=' + userid, //iframe的url
            });
        }
    });

})

function formatDuring(mss) {
    var days = parseInt(mss / (60 * 60 * 24));
    var hours = parseInt((mss % (60 * 60 * 24)) / (60 * 60));
    var minutes = parseInt((mss % (60 * 60)) / (60));
    var seconds = (mss % (1000 * 60)) / 1000;
    if (days == 0) {
        return hours + " 小时 " + minutes + " 分钟 ";
    } else if (days == 0 && hours == 0) {
        return minutes + " 分钟 ";
    } else {
        return days + " 天 " + hours + " 小时 " + minutes + " 分钟 ";
    }

}

function createTime(v) {
    var date = new Date(v * 1000);
    var y = date.getFullYear();
    var m = date.getMonth() + 1;
    m = m < 10 ? '0' + m : m;
    var d = date.getDate();
    d = d < 10 ? ("0" + d) : d;
    var h = date.getHours();
    h = h < 10 ? ("0" + h) : h;
    var M = date.getMinutes();
    M = M < 10 ? ("0" + M) : M;
    var str = y + "-" + m + "-" + d + " " + h + ":" + M;
    return str;
}