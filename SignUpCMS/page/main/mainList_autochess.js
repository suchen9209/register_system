layui.use(['form', 'layer', 'laydate', 'table', 'laytpl', 'excel','jquery'], function() {
    var form = layui.form;
    var laypage = layui.laypage;
    layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;
        excel = layui.excel;
        dataNS =  '';
     $.ajax({
            url: "http://apply.imbatv.cn/tool/init/list_header_json",
            type: "GET",
            dataType: 'json',
            success(res) {
             
            dataNS =[
                { field: 'id', title: 'ID222', align: 'center', width: 50 },
                { field: 'name', title: '姓名22222', width: 150, align: "center" },
                { field: 'nickname', title: '昵称', align: 'center' },
                { field: 'qq', title: 'qq', align: 'center' },
                { field: 'phone', title: '手机', align: 'center' },
                { field: 'email', title: '邮箱', align: 'center' },
                { field: 'game_id', title: '游戏ID', align: 'center' },
                { field: 'idcard', title: '身份证号', align: 'center' },
                {
                    field: 'extra_filed1',
                    title: '段位截图',
                    width: 180,
                    align: "center",
                    templet: function(d) {
                        return '<a href="' + d.extra_filed1 + '" target="_blank"><img src="' + d.extra_filed1 + '" height="26" /></a>';
                    }
                },
                { field: 'extra_filed2', title: '分组', align: 'center' },
                { title: '操作', width: 270, templet: '#newsListBar', fixed: "right", align: "center" }
            ];
           
            },error() {
                layer.alert('获取数据失败');
            }
        });
    var tableIns = table.render({
        elem: '#newsList',
        url: 'http://apply.imbatv.cn//tool/applicant?tid=3&state=-1',
        limit: 15,
        limits: [15, 30, 45, 60],
        page: true,
        //,…… //其他参数
        cols: [ dataNS ],
        done: function(res, curr, count) {
            $(".layui-table-box").find("[data-field='state']").css("display", "none");
            $(".layui-table-box").find("[data-field='id']").css("display", "none");
        }
    });
    // 搜索
    var $ = layui.$,
        active = {
            reload: function() {
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
            }
        };

    $('.search_btn').on('click', function() {
        exportApiDemo();
    });
    // 验证手机号
    function isPhoneNo(phone) {
        var pattern = /^1[34578]\d{9}$/;
        return pattern.test(phone);
    }
    // 导出数据
    function exportApiDemo() {
        $.ajax({
            url: "http://apply.imbatv.cn//tool/applicant?tid=3&state=-1&limit=2000",
            type: "GET",
            dataType: 'json',
            success(res) {
                var data = res.data;
                // 重点！！！如果后端给的数据顺序和映射关系不对，请执行梳理函数后导出
                data = excel.filterExportData(data, [
                    'name', 'nickname', 'qq', 'phone', 'email', 'game_id','idcard', 'extra_filed1', 'extra_filed2'
                ]);
                // 重点2！！！一般都需要加一个表头，表头的键名顺序需要与最终导出的数据一致
                data.unshift({ name: "姓名", nickname: "昵称", qq: 'qq', phone: '手机', email: '邮箱', game_id: '游戏ID',idcard: '身份证号', extra_filed1: '段位截图',extra_filed2: '分组' });

                var timestart = Date.now();
                excel.exportExcel(data, '导出接口数据.xlsx', 'xlsx');
            },
            error() {
                layer.alert('获取数据失败，请检查是否部署在本地服务器环境下');
            }
        });
    }
    //列表操作
    table.on('tool(newsList)', function(obj) {
        var layEvent = obj.event,
            data = obj.data;

        var userid = obj.data.id;
        var username = obj.data.username;
        var name = data.name;
        var phone = data.phone;
        var idcard = data.idcard;
        console.log(userid);
        if (layEvent === 'adopt') { //上机
            $.ajax({
                url: "http://apply.imbatv.cn//tool/applicant/update/" + userid,
                type: "POST",
                data: {
                    state: 10
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.code == 200) {
                        layer.msg(data.message, { time: 1000 }, function() {
                            //回调
                            window.location.reload();
                        })
                    } else {
                        layer.msg(data.message);
                    }

                },
                error: function(err) {
                    console.log(err);
                }
            });
            // 未通过
        } else if (layEvent === 'no-pass') {
            $.ajax({
                url: "http://apply.imbatv.cn//tool/applicant/update/" + userid,
                type: "POST",
                data: {
                    state: 2
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.code == 200) {
                        layer.msg(data.message, { time: 1000 }, function() {
                            //回调
                            window.location.reload();
                        })
                    } else {
                        layer.msg(data.message);
                    }

                },
                error: function(err) {
                    console.log(err);
                }
            });
            // 替补
        }else if (layEvent === 'Substitute') {
            $.ajax({
                url: "http://apply.imbatv.cn//tool/applicant/update/" + userid,
                type: "POST",
                data: {
                    state: 5
                },
                dataType: 'json',
                success: function(data) {
                    console.log(data);
                    if (data.code == 200) {
                        layer.msg(data.message, { time: 1000 }, function() {
                            //回调
                            window.location.reload();
                        })
                    } else {
                        layer.msg(data.message);
                    }

                },
                error: function(err) {
                    console.log(err);
                }
            });
        }
    });

})