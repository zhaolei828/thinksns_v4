<!DOCTYPE html>
<html lang="zh">
<head>
<meta charset="utf-8">
<title>socket test</title>
<style type="text/css">
*{ margin:0; padding:0; font-size:14px; font-family:"Microsoft YaHei","微软雅黑"; color:#333;}
body,html{ height:100%; width:100%; overflow:auto; background:#eee;}
.masbox{position:absolute; width:40%; height:90%; border:1px solid #999; top:5%; left:10%; list-style:none; background:#fafafa; overflow:hidden;}
#msglist{ height:80%; overflow:auto;}
.masbox textarea{ height:20%; box-sizing:border-box; width:80%; overflow:hidden;resize:none; border:none; border-top:1px solid #999; padding:10px; outline:none;}
.masbox .btn{ position:absolute; right:0; bottom:0; width:20%; height:20%; box-sizing:border-box; z-index:9;}
.masbox ul{ box-sizing:border-box; padding:10px; }
.masbox li{ line-height:24px; margin:1px 0; list-style:none;word-break:break-all;word-wrap:break-word; padding:5px 0; border-bottom:1px dashed #ccc;}
.masbox li.log{color:#666; text-align:left}

.config {position:absolute;top:5%;left:55%;width:20%; overflow:visible}
.config p{ overflow:visible}
.config p.mb20{ padding-bottom:20px;}
.config input{ height:26px; line-height:26px; outline:none; width:80%; box-sizing:border-box; padding:0 6px; margin-top:-1px;}
.config input.two{ width:40%;}
.config input[type=button]{ cursor:pointer}
.config input:disabled{color:#999; cursor:not-allowed}
.config form{ display:none}
.config form p{ padding-top:5px;}
.config form label{ line-height:20px;}
.config form .btn2{ padding:0; margin:0; padding:20px 0;}
#change_pack{ width:80%; height:24px; line-height:24px; box-sizing:border-box; margin-bottom:10px;}
</style>
</head>
<body>
<div class="masbox">
    <div id="msglist">
        <ul></ul>
    </div>
    <textarea id="content"></textarea>
    <input type="button" id="send" value="发送" class="btn" />
</div>
<div class="config">
    <p class="mb20"><input type="button" id="clean" value="清空右侧消息内容" /></p>
    <p><input type="text" id="host" placeholder="socket host" value="demo.thinksns.com" /></p>
    <p class="mb20"><input type="text" id="port" placeholder="socket port" value="2346" /></p>
    <p class="mb20"><input type="button" id="open" value="建立连接" class="two" /><input type="button" id="close" value="关闭连接" disabled class="two" /></p>
    <div>
        <select id="change_pack">
            <option value="">--选择数据包--</option>
            <option value="login">login(登录)</option>
            <option value="logout">logout(退出)</option>
            <option value="get_room">get_room(取得私聊房间号)</option>
            <option value="create_group_room">create_group_room(创建群聊房间)</option>
            <option value="get_room_list">get_room_list(取得房间列表)</option>
            <option value="set_room">set_room(设置房间信息)</option>
            <option value="remove_group_member">remove_group_member(移除群房间成员)</option>
            <option value="add_group_member">add_group_member(添加群房间成员)</option>
            <option value="quit_group_room">quit_group_room(退出群房间)</option>
            <option value="send_message_text">send_message(发送消息:文本)</option>
            <option value="send_message_voice">send_message(发送消息:音频)</option>
            <option value="send_message_image">send_message(发送消息:图片)</option>
            <option value="send_message_position">send_message(发送消息:位置)</option>
            <option value="send_message_card">send_message(发送消息:名片)</option>
            <option value="remove_push_message">remove_push_message(移除推送)</option>
            <option value="clear_message_unread">clear_message(清理消息:未读)</option>
            <option value="clear_message_all">clear_message(清理消息:全部)</option>
            <option value="get_message_list">get_message_list(取得消息列表)</option>
        </select>
        <!-- 登录 -->
        <form id="login_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="login" readonly /></p>
            <p><label>uid:</label><br/><input type="text" name="uid" value="32161" /></p>
            <p><label>oauth_token:</label><br/><input type="text" name="oauth_token" value="a4da040ef0ef6ed7e49faaf86a0fc884" /></p>
            <p><label>oauth_token_secret:</label><br/><input type="text" name="oauth_token_secret" value="0733b45005f60e53736701a37d15533a" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 退出 -->
        <form id="logout_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="logout" readonly /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 取得房间，私聊 -->
        <form id="get_room_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="get_room" readonly /></p>
            <p><label>uid:</label><br/><input type="text" name="uid" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 创建房间，群聊 -->
        <form id="create_group_room_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="create_group_room" readonly /></p>
            <p><label>uid_list:</label><br/><input type="text" name="uid_list" value="" /></p>
            <p><label>title:</label><br/><input type="text" name="title" value="" skip="true" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 取得房间列表 -->
        <form id="get_room_list_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="get_room_list" readonly /></p>
            <p><label>room_id:</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>mtime:</label><br/><input type="text" name="mtime" value="" skip="true" /></p>
            <p><label>limit:</label><br/><input type="text" name="limit" value="" skip="true" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 移除群房间成员 -->
        <form id="remove_group_member_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="remove_group_member" readonly /></p>
            <p><label>room_id:</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>member_uids:</label><br/><input type="text" name="member_uids" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 添加群房间成员 -->
        <form id="add_group_member_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="add_group_member" readonly /></p>
            <p><label>room_id:</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>member_uids:</label><br/><input type="text" name="member_uids" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 退出群房间 -->
        <form id="quit_group_room_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="quit_group_room" readonly /></p>
            <p><label>room_id:</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 设置房间信息 -->
        <form id="set_room_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="set_room" readonly /></p>
            <p><label>room_id:</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>title:</label><br/><input type="text" name="title" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 发消息:文本 -->
        <form id="send_message_text_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="send_message" readonly /></p>
            <p><label>message_type(消息类型):</label><br/><input type="text" name="message_type" value="text" readonly /></p>
            <p><label>room_id(房间ID):</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>content(消息内容):</label><br/><input type="text" name="content" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 发消息:音频 -->
        <form id="send_message_voice_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="send_message" readonly /></p>
            <p><label>message_type(消息类型):</label><br/><input type="text" name="message_type" value="voice" readonly /></p>
            <p><label>room_id(房间ID):</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>length(音频时长):</label><br/><input type="text" name="length" value="" /></p>
            <p><label>attach_id(附件ID):</label><br/><input type="text" name="attach_id" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 发消息:图片 -->
        <form id="send_message_image_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="send_message" readonly /></p>
            <p><label>message_type(消息类型):</label><br/><input type="text" name="message_type" value="image" readonly /></p>
            <p><label>room_id(房间ID):</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>attach_id(图片ID):</label><br/><input type="text" name="attach_id" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 发消息:位置 -->
        <form id="send_message_position_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="send_message" readonly /></p>
            <p><label>message_type(消息类型):</label><br/><input type="text" name="message_type" value="position" readonly /></p>
            <p><label>room_id(房间ID):</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>latitude(经度):</label><br/><input type="text" name="latitude" value="" /></p>
            <p><label>longitude(纬度):</label><br/><input type="text" name="longitude" value="" /></p>
            <p><label>location(位置):</label><br/><input type="text" name="location" value="" /></p>
            <p><label>attach_id(图片ID):</label><br/><input type="text" name="attach_id" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 发消息:名片 -->
        <form id="send_message_card_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="send_message" readonly /></p>
            <p><label>message_type(消息类型):</label><br/><input type="text" name="message_type" value="card" readonly /></p>
            <p><label>room_id(房间ID):</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>uid(名片用户UID):</label><br/><input type="text" name="uid" value="" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- push消息反馈 -->
        <form id="remove_push_message_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="remove_push_message" readonly /></p>
            <p><label>message_ids(消息ID):</label><br/><input type="text" name="message_ids" value="" skip="true" /></p>
            <p><label>current_room_id(当前房间):</label><br/><input type="text" name="current_room_id" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 清理消息 未读 -->
        <form id="clear_message_unread_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="clear_message" readonly /></p>
            <p><label>room_id(房间ID):</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>clear_type(清理类型):</label><br/><input type="text" name="clear_type" value="unread" readonly /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 清理消息 全部 -->
        <form id="clear_message_all_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="clear_message" readonly /></p>
            <p><label>room_id(房间ID):</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>clear_type(清理类型):</label><br/><input type="text" name="clear_type" value="all" readonly /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
        <!-- 取得消息列表-->
        <form id="get_message_list_pack">
            <p><label>type:</label><br/><input type="text" name="type" value="get_message_list" readonly /></p>
            <p><label>room_id(消息ID):</label><br/><input type="text" name="room_id" value="" /></p>
            <p><label>message_id(返回<开始ID):</label><br/><input type="text" name="message_id" value="" skip="true" /></p>
            <p><label>limit(返回数量):</label><br/><input type="text" name="limit" value="" skip="true" /></p>
            <p><label>packid:</label><br/><input type="text" name="packid" value="" skip="true" /></p>
            <p class="btn2"><input type="button" value="发送数据包" class="send_pack two" /><input type="button" value="生成数据包" class="create_pack two" /></p>
        </form>
    </div>
</div>
<div></div>
<script type="text/javascript" src="jquery.js"></script>
<script type="text/javascript" src="weksocket.js"></script>
<script type="text/javascript" src="json.js"></script>
<script type="text/javascript">
$(function() {
    var websocket = null;
    var pongData = function(){
        if(window.pongTimer){
            clearTimeout(window.pongTimer);
        }
        window.pongTimer = setTimeout(function(){
            if(websocket){
                websocket.send('{"type":"pong"}');
            }
            pongData();
        }, 18000);
    }
    var options = {
		domain  : null,
		port    : 0,
		protocol: '',
		onOpen : function(event) {
            pongData();
			insertLog('打开Socket连接：ws://' + this.domain + ':' + this.port);
		},
		onSend : function(message) {
            pongData();
			insertLog('发送数据包：'+message);
		},
		onMessage : function(message) {
            insertLog('服务器数据包：'+message);
            if(message == '{"type":"ping"}'){
                websocket.send(JSON.stringify({"type":"pong"}));
            }
		},
		onError : function(event) {
			console.log(event);
			insertLog('WebSocket错误：view console');
		},
		onClose : function(event) {
            if(!websocket) return;
			websocket = null;
            $('#close').attr('disabled', true);
            $('#open').attr('disabled', false);
            insertLog('WebSocket关闭：ws://'+this.domain + ':' + this.port);
		}
	};
    var uArgs  = new Array();
    var listId = 0;

    //清空
    $('#clean').click(function(){
        $('#msglist ul').empty();
    });

    //连接
    $('#open').click(function(){
        var i,input,
        inputs = ['host', 'port'];
        for(i in inputs) {
            input = $('input#'+inputs[i]);
            input.val($.trim(input.val()));
            if(input.val().length <= 0){
                input.focus();return;
            }
        }
        options.domain =  $('input#host').val();
        options.port   =  $('input#port').val();
        if(websocket){
            websocket.close();
            websocket = null;
        }
        websocket = $.websocket(options);
        if(websocket){
            $(this).attr('disabled', true);
            $('#close').attr('disabled', false);
        }
    });

    //断开连接
    $('#close').click(function(){
        if(websocket){
            websocket.close();
            websocket = null;
        }
        $(this).attr('disabled', true);
        $('#open').attr('disabled', false);
    });

    //数据包 快捷发送或生成
    $('#change_pack').change(function(e) {
        var val = $(this).val();
        $(this).nextAll().hide();
        if(!val) return;
        if($('#'+val+'_pack').length > 0){
            $('#'+val+'_pack').show();
        }
    });
    $('.send_pack,.create_pack').click(function(){
        var form = $(this).parents('form');
        var inputs = form.find('input:text');
        var json = {};
        inputs.each(function(i, input){
            var input = $(input);
            var val = $.trim(input.val());
            if(!input.attr('skip') && !val){
                json = false;
                input.focus();
                return false;
            }
            if(!(input.attr('skip') && !val)){
                json[input.attr('name')] = val;
            }
        });
        if(!json) return false;
        json = JSON.stringify(json);
        if($(this).hasClass('send_pack')){
            if(websocket){
                websocket.send(json);
            }else{
                insertLog('发送失败：请先连接服务器');
            }
        }else{
            $('#content').val(json);
        }
        return false;
    });



    //发送数据包
    $('#send').click(function(){
        var content = $.trim($('#content').val());
        if(content.length <= 0) {
            $('#content').focus();
            return;
        }
        if(websocket){
            websocket.send(content);
        }else{
            insertLog('发送失败：请先连接服务器');
        }
    });
    
    var ul = $('#msglist ul');
    var insertLi = function(content, type){
        type = type || '';
        ul.append('<li class="'+type+'">'+content+'</li>');
        $('#msglist').stop().animate({
            scrollTop: ul.height()
        }, 'slow');
    }
    var insertLog = function(content){
        insertLi(content, 'log');
    }
});
</script>
</body>
</html>