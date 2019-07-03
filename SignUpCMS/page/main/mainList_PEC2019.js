layui.use(['form', 'layer', 'laydate', 'table', 'laytpl', 'excel','jquery'], function() {
    var form = layui.form;
    var laypage = layui.laypage;
    layer = parent.layer === undefined ? layui.layer : top.layer,
        $ = layui.jquery,
        laydate = layui.laydate,
        laytpl = layui.laytpl,
        table = layui.table;
        excel = layui.excel;

    var tableIns = table.render({
        elem: '#newsList',
        url: 'http://apply.imbatv.cn//tool/applicant?tid=4&state=-1',
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
                { field: 'extra_filed3', title: '战队名称', align: 'center' },
                { title: '操作', width: 270, templet: '#newsListBar', fixed: "right", align: "center" }
            ]
        ],
        done: function(res, curr, count) {
            console.log(res);
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
            url: "http://apply.imbatv.cn//tool/applicant?tid=4&state=-1&limit=2000",
            type: "GET",
            dataType: 'json',
            success(res) {
                var data = res.data;
                // 重点！！！如果后端给的数据顺序和映射关系不对，请执行梳理函数后导出
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
        }
    });

})