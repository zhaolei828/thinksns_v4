



function doFav(info_id) {
    if (MID != 0) {
        $.post(U('cat/Index/_doFav'), {'id': info_id}, function (msg) {
            var btn = $('#cat_btn_fav_' + info_id);
            if (msg.status == 1) {
                ui.success('收藏成功。');

                btn.removeClass('c_fav_likebf');
                btn.addClass('c_fav_liked');
                btn.attr('title', '取消收藏');
                $('#c_info_fav_num_' + info_id).text(parseInt($('#c_info_fav_num_' + info_id).text()) + 1);

            }
            else if (msg.status == 2) {
                ui.success('取消收藏成功。');
                btn.attr('title', '收藏');
                btn.addClass('c_fav_likebf');
                btn.removeClass('c_fav_liked');
                $('#c_info_fav_num_' + info_id).text(parseInt($('#c_info_fav_num_' + info_id).text()) - 1);
            }
            else if (msg.status == 3) {
                ui.error('不能收藏自己发布的内容。');
            }
            else {
                ui.error('未知情况，处理失败。');
            }
        }, 'json');
    }
    else {
        ui.error('请登陆后收藏。');
    }
}
function get_s_Infos(obj, entity_id, uid, info_id) {
    $(obj).parent().parent().find('li').removeClass('c_ul_s_active');
    $(obj).parent().addClass('c_ul_s_active');
    $('.c_ul_s_infos').load(U('cat/Index/_get_s_infos') + '&entity_id=' + entity_id + '&info_id=' + info_id + '&uid=' + uid);
}
function get_Infos(obj,entity_id){
    $(obj).parent().parent().find('li').removeClass('c_ul_s_active');
    $(obj).parent().addClass('c_ul_s_active');
    $('.c_ul_s_infos').load(U('cat/Center/_get_infos') + '&entity_id=' + entity_id);
    $('#hd_info_id').val(0);
}
function set_selected(obj,info_id){
    $(obj).parent().parent().find('li').removeClass('c_ul_s_active');
    $(obj).parent().addClass('c_ul_s_active');
    $('#hd_info_id').val(info_id);
}

/**
 *
 * @param uid 接受者ID
 * @param info_id 需要发送的信息ID
 * @param s_info_id 源信息Id
 */
function send_info(uid, info_id, s_info_id) {
    $.post(U('cat/Index/_send_info'), {uid: uid, info_id: info_id, s_info_id: s_info_id}, function (msg) {
        if (msg.status) {
            ui.box.close();
            ui.success('发送成功。可到个人中心可以收回信息。要注意保护自己的隐私。');
        }
        else {
            ui.box.close();
            ui.error('发送失败。');
        }
    }, 'json');
}
jQuery.extend(ui.box, {
    query: function (content, title, ok, cancel) {
        ui.box.show('<div style="padding: 20px;">' +
            '<div class="mb10" style="min-width: 180px;min-height: 60px;font-size: 12px;">' + content +
            '</div>' +
            '<div class="right" ><a id="query_ok" class="mr10 btn-green-big">确定</a>' +
            '<a id="query_cancel" class=" btn-grey-white"><span>取消</span></a></div>' +
            '<div class="clearfix"></div>', title);
        $('#query_ok').click(function () {
            ok();
        });
        $('#query_cancel').click(function () {
            ui.box.close();
            cancel();
        });
    }
});
M.addEventFns({
    'post_com': {
        click: function () {
            if (MID == 0) {
                ui.error('请登陆后发表评论。');
                return;
            }
            var oArgs = M.getEventArgs(this);
            $.post(U('cat/Index/_doCom'), {content: editor.getContent(), info_id: oArgs.id}, function (msg) {
                if (msg.status) {
                    editor.setContent('');
                    $('#c_no_com').html('');
                    $('#c_com').prepend(msg.data);
                    ui.success('评论发表成功。');

                }
                else {
                    ui.error('评论发表失败。');
                }
            }, 'json');
        }
    },
    'del_info': {
        click: function () {
            var oArgs = M.getEventArgs(this);
            ui.box.query('确定删除该信息？', '删除信息', function () {

                $.post(U('cat/Index/_delInfo'), {info_id: oArgs.id}, function (msg) {
                    if (msg.status) {
                        ui.box.close();
                        ui.success('删除成功。');
                        setTimeout(function () {
                                location.href = U('cat/Index/li') + '&name=' + oArgs.entity;
                            }, 1000
                        );
                    } else {
                        ui.error('删除失败。');
                    }

                }, 'json');
            });
        }
    },
    'send_info': {
        click: function () {
            var oArgs = M.getEventArgs(this);
            ui.box.show($('#box_send_entitys').html(), '选择发送的信息');
        }
    },
    'read_info': {
        click: function () {

            var oArgs = M.getEventArgs(this);
            $.post(U('cat/Center/_doRead'), {send_id: oArgs.id}, 'json');
        }
    },
    'get_back': {
        click: function (obj) {
            var oArgs = M.getEventArgs(this);
            $.post(U('cat/Center/_doGetBack'), {send_id: oArgs.id}, function (msg) {
                if (msg.status) {
                    ui.success('撤回成功！');
                    $('#s_' + oArgs.id).remove();
                }
                else {
                    ui.error('撤回失败。');
                }
            }, 'json');
        }
    },
    'post_info':{
        click:function(){
            if($('#search_uids').val()=='')
            {
                ui.error('请选择信息接收者。');
                return;
            }
            if($('#hd_info_id').val()==0){
                ui.error('请选择发送的信息。');
                return;
            }
            editor.sync();
            $('#frm_main').submit();
        }
    }

})
;