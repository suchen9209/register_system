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
        url: '/tool/user',
        limit: 15,
        limits: [15, 30, 45, 60],
        page: true,
        //,…… //其他参数
        cols: [
            [
                { field: 'id', title: 'ID', align: 'center'},
                { field: 'name', title: '姓名', align: "center" },
                { field: 'username', title: '用户名', align: 'center' },
                { field: 'weight', title: '权限', align: 'center' },
                { field: 'createtime', title: '创建时间', align: 'center' },
                { field: 'tournament', title: '赛事', align: 'center' },
                { title: '操作',  templet: '#newsListBar', fixed: "right", align: "center" }
            ]
        ],
        done: function(res, curr, count) {
            console.log(res);
            $(".layui-table-box").find("[data-field='state']").css("display", "none");
            $(".layui-table-box").find("[data-field='id']").css("display", "none");
        }
    });
    //修改用户列表信息
    function modify(edit){
        var index = layui.layer.open({
            title : "修改用户列表信息",
            type : 2,
            content : "modify.html",
            success : function(layero, index){
                var body = layui.layer.getChildFrame('body', index);
                if(edit){
                    body.find(".item1 input").val(edit.name);
                    body.find(".item2 input").val(edit.username);
                    body.find(".item3 input").val(edit.weight);
                    body.find(".item4").attr("type",edit.type);
                }
                setTimeout(function(){
                    layui.layer.tips('点击此处返回商品列表', '.layui-layer-setwin .layui-layer-close', {
                        tips: 3
                    });
                },2000)
            }
        })
        layui.layer.full(index);
        //改变窗口大小时，重置弹窗的宽高，防止超出可视区域（如F12调出debug的操作）
        // $(window).on("resize",function(){
        //     layui.layer.full(index);
        // })
    }
    $(window).one("resize",function(){
        $(".commondityAdd_btn").click(function(){
            var index = layui.layer.open({
                title : "增加优惠劵",
                type : 2,
                content : "commondityAdd.html",
                success : function(layero, index){
                    setTimeout(function(){
                        layui.layer.tips('返回', '.layui-layer-setwin .layui-layer-close', {
                            tips: 3
                        });
                    },2000)
                }
            })          
            layui.layer.full(index);
        })
    }).resize();
    //列表操作
    table.on('tool(newsList)', function(obj) {
        var layEvent = obj.event,
            data = obj.data;
        var list = data.detail;
        var order_id = obj.data.id;
        if (layEvent === 'modify') { //修改
            modify(data);
        } 
    });

})