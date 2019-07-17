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
        url: '/tool/tournament',
        limit: 15,
        limits: [15, 30, 45, 60],
        page: true,
        //,…… //其他参数
        cols: [
            [
                { field: 'id', title: 'ID', align: 'center'},
                { field: 'name', title: '名称', align: "center" },
                { field: 'now_num', title: '当前报名人数', align: 'center' },
                { field: 'need_num', title: '报名人数上限 ', align: 'center' },
                { field: 'starttime', title: '开始时间', align: 'center' },
                { field: 'endtime', title: '结束时间', align: 'center' },
                { title: '操作',  templet: '#newsListBar', fixed: "right", align: "center" }
            ]
        ],
        done: function(res, curr, count) {
            console.log(res);
            $(".layui-table-box").find("[data-field='state']").css("display", "none");
            $(".layui-table-box").find("[data-field='id']").css("display", "none");
        }
    });
    //修赛事列表信息
    function modify(edit){
        console.log(edit);
        var index = layui.layer.open({
            title : "修改赛事列表信息",
            type : 2,
            content : "modify.html",
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                if(edit){
                    body.find(".item1 input").val(edit.name);
                    // body.find(".item2 input").val(edit.now_num);
                    body.find(".item3 input").val(edit.need_num);
                    body.find(".item4 input").val(edit.starttime);
                    body.find(".item5 input").val(edit.endtime);
                    body.find(".item6").attr("data-id",edit.id);
                }
                setTimeout(function(){
                    layui.layer.tips('点击此处返回赛事列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },1000)
            }
        })
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        // $(window).on("resize",function(){
        //     layui.layer.full(index);
        // })
    }
    //修赛事列表信息
    function set_up(edit){
        console.log(edit);
        json = JSON.stringify(edit);
        var index = layui.layer.open({
            title : "修改赛事",
            type : 2,
            content : "set_up.html?tid="+edit.id,
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                // if(edit){
                //     body.find(".spc").attr("data-id",edit.id);
                // }
                setTimeout(function(){
                    layui.layer.tips('点击此处返回赛事列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },1000)
            }
        })
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        // $(window).on("resize",function(){
        //     layui.layer.full(index);
        // })
    }
    //列表操作
    table.on('tool(newsList)', function(obj) {
        var layEvent = obj.event,
            data = obj.data;
        var list = data.detail;
        var order_id = obj.data.id;
        if (layEvent === 'modify') { //修改
            modify(data);
        }else if(layEvent === 'set_up') {//设置
            set_up(data);
        }
    });

})